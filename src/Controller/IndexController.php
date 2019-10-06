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

    /**
     * @param Request $request
     * @param TranslatorInterface $translator
     * @return JsonResponse
     * @Route("/api/talking/subscribe", name="api_talking_subscribe", methods={"GET"})
     */
    public function getTalkingThreat(Request $request, TranslatorInterface $translator){
        $data = [
            'first' => [
                $translator->trans('talking.threat.first', [], null, $request->getLocale()),
                $translator->trans('talking.threat.first.red', [], null, $request->getLocale())
                ],
            'firstButton' => [
                'lovely' => [
                    'value' => 'lovely',
                    'text' => $translator->trans('lovely', [], null, $request->getLocale())
                ],
                'friendly' => [
                    'value' => 'friendly',
                    'text' => $translator->trans('friendly', [], null, $request->getLocale())
                ],
                'both' => [
                    'value' => 'both',
                    'text' => $translator->trans('both', [], null, $request->getLocale())
                ]
            ],
            'second' => [
                $translator->trans('talking.threat.second', [], null, $request->getLocale()),
                $translator->trans('talking.threat.second.red', [], null, $request->getLocale())
            ],
            'secondButton' => [
                'daddy' => [
                    'value' => true,
                    'text' => $translator->trans('daddy', [], null, $request->getLocale())
                ],
                'mom' => [
                    'value' => false,
                    'text' => $translator->trans('mom', [], null, $request->getLocale())
                ]
            ],
            'third' => [
                $translator->trans('talking.threat.third', [], null, $request->getLocale()),
                $translator->trans('talking.threat.third.red', [], null, $request->getLocale())
            ],
            'thirdButton' => [
                'text' => $translator->trans('validate', [], null, $request->getLocale())
            ],
            'thirdError' => [
                'text' => $translator->trans('talking.threat.third.error', [], null, $request->getLocale())
            ],
            'fourth' => [
                'text' => [
                    $translator->trans('talking.threat.fourth', [], null, $request->getLocale()),
                    $translator->trans('talking.threat.fourth.red', [], null, $request->getLocale())
                ],
                'response' => [
                    $translator->trans('talking.threat.fourth.response', [], null, $request->getLocale())
                ],
                'labels' => [
                    'canton' => $translator->trans('talking.threat.fourth.label.canton', [], null, $request->getLocale()),
                    'city' => $translator->trans('talking.threat.fourth.label.city')
                ]
            ],
            'fifth' => [
                $translator->trans('talking.threat.fifth', [], null, $request->getLocale()),
                $translator->trans('talking.threat.fifth.red', [], null, $request->getLocale())
            ],
            'sixth' => [
                $translator->trans('talking.threat.sixth', [], null, $request->getLocale()),
                $translator->trans('talking.threat.sixth.red', [], null, $request->getLocale())
            ],
            'sixthButton' => [
                'text' => $translator->trans('validate', [], null, $request->getLocale())
            ],
            'seventh' => [
                $translator->trans('talking.threat.seventh', [], null, $request->getLocale()),
                $translator->trans('talking.threat.seventh.red', [], null, $request->getLocale()),
                $translator->trans('talking.threat.seventh.confirm', [], null, $request->getLocale())
            ],
            'seventhButton' => [
                'text' => $translator->trans('validate', [], null, $request->getLocale())
            ],
            'seventhError' => [
                'text' => $translator->trans('talking.threat.seventh.error', [], null, $request->getLocale())
            ],
            'final' => [
                $translator->trans('talking.threat.final', [], null, $request->getLocale())
            ]
        ];
        return $this->json($data);
    }

    /**
     * @param Request $request
     * @param TranslatorInterface $translator
     * @return JsonResponse
     * @Route("/api/baseline", name="api_baseline", methods={"GET"})
     */
    public function baseline(Request $request, TranslatorInterface $translator){
        $data = [
            'baseline' => [
                $translator->trans('baseline', [], null, $request->getLocale()),
                $translator->trans('baseline.red', [], null, $request->getLocale())
            ]
        ];
        return $this->json($data);
    }
}
