<?php


namespace App\Controller;


use phpDocumentor\Reflection\Types\Self_;
use phpDocumentor\Reflection\Types\This;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

class RegistrationController extends AbstractController
{
    private $_serializer;
    public function __construct()
    {
        $encoders = [new XmlEncoder(), new JsonEncoder()];
        $normalizers = [new ObjectNormalizer()];
        $this->_serializer = new Serializer($normalizers, $encoders);
    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     * @throws \Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface
     * @Route("/api/register", name="app_register", methods={"POST"})
     */
    public function index(Request $request){
        $clientIp = $request->headers->get('x-real-ip');
        if (empty($clientIp)){
            return $this->json(['data' => ['error' => 'You must be a Swissman to be registered if you use a VPN you have to deactivate it to register']]);
        }
        if (!self::checkToken($request)){
            return $this->json(['data' => ['error' => 'bad token']]);
        }
        $client = HttpClient::create();
        $response = $client->request('GET', $this->getParameter('api.geo.uri') . '/' . $clientIp);
        $data = $this->_serializer->decode($response->getContent(), 'json');
        if ($data['countryCode'] == 'CH' || $data['countryCode'] == 'FR'){ //TODO retirer le fr à la fin
            return $this->json($data);
        }
        return $this->json(['data' => ['error' => 'You must be a Swissman to be registered if you use a VPN you have to deactivate it to register']]);
    }

    private function checkToken(Request $request){
        $content = $request->getContent();
        $data = $this->_serializer->decode($content, 'json');
        return ($this->isCsrfTokenValid('register', $data['token'])) ? true : false;
    }
}