<?php

namespace App\Controller;

use App\Async\SixProcess;
use App\Entity\Items;
use App\Entity\Payment;
use App\Entity\PaymentProfil;
use App\Entity\User;
use App\Service\ItemsService;
use App\Service\SubscribeService;
use backndev\sixpayment\SixPayment;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Finder\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\HttpFoundation\Session\Session;


class PaymentController extends AbstractController
{
    private $_serializer;

    public function __construct()
    {
        $encoders = [new XmlEncoder(), new JsonEncoder()];
        $normalizers = [new ObjectNormalizer()];
        $this->_serializer = new Serializer($normalizers, $encoders);
    }

    /**
     * @param int $itemId
     * @param Session $session
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/payment/{itemId}", name="payment")
     */
    public function index(int $itemId, Session $session)
    {
        $em = $this->getDoctrine()->getRepository(Items::class);
        $item = $em->find($itemId);
        $data['amount'] = $item->getPrice();
        $data['context'] = $item->getType();
        $data['user'] = $this->getUser()->getEmail();
        $data['itemId'] = $item->getId();
        $data['currency'] = 'CHF';
        $session->set('itemId', $itemId);
        return $this->render('payment/index.html.twig', ['settings' => $data]);
    }

    /**
     * @param Request $request
     * @param Session $session
     * @param ItemsService $itemsService
     * @param SubscribeService $subscribeService
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     * @throws \Exception
     * @Route("/api/card", name="payment_card_credentials", methods={"POST"})
     */
    public function getCardCredentials(Request $request, Session $session, ItemsService $itemsService, SubscribeService $subscribeService){
        if (!$request->isMethod('POST') || !self::checkToken($request)){
            throw new AccessDeniedException();
        }
        $content = $this->_serializer->decode($request->getContent(), 'json');
        $data['credentials'] = $content['credentials'];
        $data['amount'] = $content['settings']['amount'];
        $data['context'] = $content['settings']['context'];
        $data['currency'] = $content['settings']['currency'];
        $six = self::createSixInstance();
        $payment = json_decode($six->createDirectPayment($data));
        /** @var User $user */
        $user = $this->getUser();
        $em = $this->getDoctrine()->getManager();

        $pay = new PaymentProfil();
        $pay->setUser($user);
        $pay->setAlias($payment->alias);
        $pay->setCardName($payment->PaymentMeans->Brand->Name);
        $pay->setDisplayText($payment->PaymentMeans->DisplayText);
        $pay->setExpMonth($payment->PaymentMeans->Card->ExpMonth);
        $pay->setExpYear($payment->PaymentMeans->Card->ExpYear);
        if (!$user->getPaymentProfil()){
            $pay->setSelected(true);
        }
        else{
            foreach ($user->getPaymentProfil() as $card){
                $card->setSelected(false);
            }
            $pay->setSelected(true);
        }
        $em->persist($pay);
        $item = $em->getRepository(Items::class)->find($session->get('itemId'));
        if ($item->getIsASubscribe()){
            if($subscribeService->setSixSubscription($user, $item->getId(), $payment->alias)){
                return $this->json(['success' => 'You will be logout to activate your subscription']);
            }
            return $this->json(['error' => 'An error as been throw during your payment']);
        }
        $paid = new Payment();
        $paid->setPaymentProfil($pay);
        $paid->setUniqKey($payment->Transaction->Id);
        $paid->setMethod('six');
        $paid->setIsCaptured(false);
        $paid->setUser($user);
        $paid->setDate(new \DateTime('now'));
        $paid->addItem($item);
        $paid->setIsAccepted(true);
        $em->persist($paid);
        $em->flush();

        $itemsService->createItem($item->getId(), $user->getId());
        $session->remove('itemId');
        return $this->json($this->_serializer->encode($payment, 'json'));
    }

    /**
     * @param Request $request
     * @param Session $session
     * @param ItemsService $itemsService
     * @param SubscribeService $subscribeService
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     * @throws \Exception
     * @Route("/api/payment/knowcard", name="api_know_card", methods={"POST"})
     */
    public function payWithAlias(Request $request, Session $session, ItemsService $itemsService, SubscribeService $subscribeService){
        if (!self::checkToken($request) || !$request->isMethod('POST')){
            throw new AccessDeniedException();
        }
        $em = $this->getDoctrine()->getManager();
        $data = $this->_serializer->decode($request->getContent(), 'json');
        /** @var User $user */
        $user = $this->getUser();
        $cards = $user->getPaymentProfil();
        $usedCard = $em->getRepository(PaymentProfil::class)->findOneBy(['alias' => $data['alias']]);
        foreach ($cards as $card){
            if ($usedCard->getId() !== $card->getId() && $card->getSelected() === true){
                $card->setSelected(false);
                $em->persist($card);
                $usedCard->setSelected(true);
                $em->persist($usedCard);
                $em->flush();
            }
        }
        $six = self::createSixInstance();
        $response = json_decode($six->createAliasPayment($data['alias'], $data['settings']['amount'], $data['settings']['context']));
        $paid = new Payment();
        $item = $em->getRepository(Items::class)->find($session->get('itemId'));
        if ($item->getIsASubscribe()){
            if($subscribeService->setSixSubscription($user, $item->getId(), $usedCard->getAlias())){
                return $this->json(['success' => 'You will be logout to activate your subscription']);
            }
            return $this->json(['error' => 'An error as been throw during your payment']);
        }
        $paid->setPaymentProfil($usedCard);
        $paid->setUniqKey($response->Transaction->Id);
        $paid->setMethod('six');
        $paid->setIsCaptured(false);
        $paid->setDate(new \DateTime('now'));
        $paid->setIsAccepted(true);
        $paid->setUser($user);
        $paid->addItem($item);

        $em->persist($paid);
        $em->flush();

        $itemsService->createItem($item->getId(), $user->getId());
        $session->remove('itemId');
        return $this->json(json_encode($response));
    }

    /**
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     * @Route("/api/payment/profil", name="api_know_card_profil", methods={"GET"})
     */
    public function checkIfKnowCard(){
        /** @var User $user */
        $user = $this->getUser();
        if(count($user->getPaymentProfil()) > 0){
            $cards = [];
            foreach ($user->getPaymentProfil() as $paymentProfil){
                $card['expMount'] = $paymentProfil->getExpMonth();
                $card['expYear'] = $paymentProfil->getExpYear();
                $card['alias'] = $paymentProfil->getAlias();
                $card['displayText'] = $paymentProfil->getDisplayText();
                $card['cardName'] = $paymentProfil->getCardName();
                $card['id'] = $paymentProfil->getId();
                array_push($cards,$card);
            }
            return $this->json($cards);
        }
        return $this->json([]);
    }

    private function createSixInstance(){
        return new SixPayment($this->getParameter('api.six.customer'),
            $this->getParameter('api.six.terminal'),
            rand(1, 100),
            $this->getParameter('api.six.key'),
            $this->getParameter('api.six.uri'));
    }

    private function checkToken(Request $request){
        $data = $this->_serializer->decode($request->getContent(), 'json');
        $submittedToken = $data['token'];
        if($this->isCsrfTokenValid('payment', $submittedToken)){
            return true;
        }
        return false;
    }

    /**
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     * @Route("/api/cron/capture", name="api_cron_capture")
     */
    public function doACapture(){
        SixProcess::capture();
        return $this->json('ok');
    }
}
