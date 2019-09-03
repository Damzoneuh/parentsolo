<?php

namespace App\Controller;

use App\Entity\Items;
use backndev\paypal\PayPal;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;


//api.pp.client: 'ASvKT0PELf02c8czgV7bc9gMOHoFta4w8rudflInCUSi01sHgZxST_rYzpajTQeezYovoL8P06CagPj-'
//    api.pp.secret: 'EGItmyzLAe9iSzvugak1tyyIIYMnQwJWNxVJc0zhv0fwfE1FBcYP216kHqI5GNYX06qu7Nh-HfApJEYb'
//    api.pp.uri: 'https://api.sandbox.paypal.com'

class PaypalController extends AbstractController
{
    private $_serializer;
    private $paypal;
    public function __construct()
    {
        $encoders = [new XmlEncoder(), new JsonEncoder()];
        $normalizers = [new ObjectNormalizer()];
        $this->_serializer = new Serializer($normalizers, $encoders);
        //$this->paypal = new PayPal($this->getParameter('api.pp.client'), $this->getParameter('api.pp.secret'), $this->getParameter('api.pp.uri'));
    }


    /**
     * @Route("/paypal/{id}", name="paypal")
     */
    public function index($id){
        $em = $this->getDoctrine()->getManager();
        $item = $em->getRepository(Items::class)->find($id);

        return $this->render('paypal/index.html.twig',
            ['client' => $this->getParameter('api.pp.client'),
                'price' => $item->getPrice(),
                'subscribe' => $item->getIsASubscribe()
            ]);
    }

    /**
     * @Route("api/paypal", name="api_paypal", methods={"POST"})
     */
    public function setDirectOrder()
    {
        return $this->json('cool ');
    }

    private function checkToken(Request $request){
        $data = $this->_serializer->decode($request->getContent(), 'json');
        $submittedToken = $data['token'];
        if($this->isCsrfTokenValid('payment', $submittedToken)){
            return true;
        }
        return false;
    }
}
