<?php

namespace App\Controller;

use App\Async\SixProcess;
use App\Entity\Payment;
use App\Entity\PaymentProfil;
use App\Entity\User;
use backndev\sixpayment\SixPayment;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Finder\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;


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
     * @Route("/payment", name="payment")
     */
    public function index()
    {
        $data['amount'] = 100;
        $data['context'] = 'test';
        $data['currency'] = 'CHF';
        return $this->render('payment/index.html.twig', ['settings' => $data]);
    }

    /**
     * @Route("/api/card", name="payment_card_credentials", methods={"POST"})
     * @throws \Symfony\Component\Serializer\Exception\ExceptionInterface
     */
    public function getCardCredentials(Request $request){
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

        $paid = new Payment();
        $paid->setPaymentProfil($pay);
        $paid->setUniqKey($payment->Transaction->Id);
        $paid->setIsCaptured(false);
        $em->persist($paid);
        $em->flush();

        return $this->json($this->_serializer->encode($payment, 'json'));
    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     * @Route("/api/payment/knowcard", name="api_know_card", methods={"POST"})
     */
    public function payWithAlias(Request $request){
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
        $response = $six->createAliasPayment($data['alias']);
        return $this->json($response);
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
