<?php

namespace App\Controller;

use App\Async\CreateItem;
use App\Entity\Items;
use App\Entity\Payment;
use App\Entity\User;
use App\Service\ItemsService;
use backndev\paypal\PayPal;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Process\Process;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

class PaypalController extends AbstractController
{
    private $_serializer;
    public function __construct()
    {
        $encoders = [new XmlEncoder(), new JsonEncoder()];
        $normalizers = [new ObjectNormalizer()];
        $this->_serializer = new Serializer($normalizers, $encoders);
    }


    /**
     * @param $id
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws ClientExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     * @Route("/paypal/{id}", name="paypal")
     */
    public function index($id){
        $em = $this->getDoctrine()->getManager();
        $item = $em->getRepository(Items::class)->find($id);
        $data = [
            'client' => $this->getParameter('api.pp.client'),
            'price' => $item->getPrice(),
            'subscribe' => $item->getIsASubscribe(),
            'id' => $id
        ];
        if (true === $item->getIsASubscribe()){
            $paypal = self::createPaypalInstance();
            $plan = $this->_serializer->decode($paypal->setSubscription($item), 'json');
            $data['plan'] = $plan['id'];
        }
        return $this->render('paypal/index.html.twig', $data);
    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     * @throws ClientExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     * @Route("api/paypal/complete", name="api_paypal", methods={"POST"})
     */
    public function setDirectOrder(Request $request)
    {
        $data = $this->_serializer->decode($request->getContent(), 'json');
        $paypal = self::createPaypalInstance();
        $capture = $this->_serializer->decode($paypal->setCapture($data['details']['id']), 'json');
        $em = $this->getDoctrine()->getManager();
        /** @var User $user */
        $user = $this->getUser();
        CreateItem::createItem($user->getId(), $data['item']);
        $payment = new Payment();
        $payment->setIsCaptured(true);
        $payment->setUniqKey($data['details']['id']);
        $payment->setMethod('paypal');
        $payment->setUser($user);
        $em->persist($payment);
        $em->flush();
        return $this->json($capture);
    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     * @throws ClientExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     * @Route("/api/paypal/approuve/sub", name="api_approuve_sub")
     */
    public function approuveSubscribe(Request $request){
        $data = $this->_serializer->decode($request->getContent(), 'json');
        $item = $this->getDoctrine()->getRepository(Items::class)->find($data['item_id']);
        $sub = self::createPaypalInstance();
        $subscrib = $sub->approuveSubscription($item, $this->getUser(), $data['plan_id']);
        return $this->json($subscrib);
    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     * @Route("/paypal/accept/sub", name="paypal_accept_sub")
     */
    public function acceptSubscribe(Request $request){
        //TODO stocké le sub ici
        return $this->json([$request->get('subscription_id')]);
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
     * @return PayPal
     * @throws ClientExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    private function createPaypalInstance(){
        return new PayPal($this->getParameter('api.pp.client'), $this->getParameter('api.pp.secret'), $this->getParameter('api.pp.uri'));
    }
}
