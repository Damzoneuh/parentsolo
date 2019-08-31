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
use Symfony\Component\Process\Process;
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
        $alias = $six->createAlias($data);
        $alias = json_decode($alias);
        /** @var User $user */
        $user = $this->getUser();
        $em = $this->getDoctrine()->getManager();

        $pay = new PaymentProfil();
        $pay->setUser($user);
        $pay->setAlias($alias->Alias->Id);
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
     * @Route("/api/payment/knowcard", name="api_know_card")
     */
    public function payWithAlias(Request $request){
        //TODO create payment with a know card
        return $this->json('cool');
    }

    /**
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     * @Route("/api/payment/profil", name="api_know_card_profil")
     */
    public function checkIfKnowCard(){
        /** @var User $user */
        $user = $this->getUser();
        if(count($user->getPaymentProfil()) > 0){
            $cards = [];
            foreach ($user->getPaymentProfil() as $paymentProfil){
                array_push($cards, $paymentProfil);
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
