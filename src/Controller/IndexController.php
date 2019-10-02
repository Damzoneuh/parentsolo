<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Translation\Translator;
use Symfony\Contracts\Translation\TranslatorInterface;

class IndexController extends AbstractController
{
    private $_serializer;

    public function __construct()
    {
        $encoders = [new XmlEncoder(), new JsonEncoder()];
        $normalizers = [new ObjectNormalizer()];
        $this->_serializer = new Serializer($normalizers, $encoders);
    }

    /**
     * @Route("/", name="index")
     */
    public function index()
    {
        return $this->render('index/index.html.twig');
    }

    /**
     * @param TranslatorInterface $translator
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     * @Route("/api/nav", name="api_get_nav", methods={"GET"})
     */
    public function getNavBar(TranslatorInterface $translator, Request $request){
        $links = [
            'home' => [
                'name' => $translator->trans('home.link', [], null, $request->getLocale()),
                'path' => '/'
            ],
            'testimony' => [
                'name' => $translator->trans('testimony.link', [], null, $request->getLocale()),
                'path' => '/testimony'
            ],
            'faq' => [
                'name' => 'FAQ',
                'path' => '/faq'
            ]
        ];
        $connection = [];
        if ($this->getUser()){
            $connection['path'] = '/logout';
            $connection['name'] = $translator->trans('logout.link', [], null, $request->getLocale());
        }
        else{
            $connection['path'] = '/login';
            $connection['name'] = $translator->trans('connection.link', [], null, $request->getLocale());
        }
        $lang = [
            'fr' => [
                'name' => 'fr',
            ],
            'de' => [
                'name' => 'de'
            ],
            'en' => [
                'name' => 'en'
            ],
            'selected' => $request->getLocale()
        ];

        $res = ['lang' => $lang, 'links' => $links, 'connection' => $connection];
        return $this->json($res);
    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     * @Route("/api/lang", name="api_set_local", methods={"POST"})
     */
    public function setLocal(Request $request){
        $data = $this->_serializer->decode($request->getContent(), 'json');
        $response = new JsonResponse();
        $response->headers->setCookie(Cookie::create('_locale', $data['lang']));
        $response->setJson((string)'ok');
        $response->send();

        return $response;
    }
}
