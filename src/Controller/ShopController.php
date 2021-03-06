<?php

namespace App\Controller;

use App\Entity\Items;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

class ShopController extends AbstractController
{
    /**
     * @Route("/shop", name="shop", methods={"GET"})
     */
    public function index(Request $request, TranslatorInterface $translator)
    {
        $em = $this->getDoctrine()->getRepository(Items::class);
        $items = $em->findAll();
        $data = [];
        $row = [];
        foreach ($items as $item){
            $row['type'] = $item->getType();
            $row['id'] = $item->getId();
            $row['isASubscribe'] = $item->getIsASubscribe();
            $row['price'] = $item->getPrice();
            array_push($data, $row);
        }
        return $this->render('shop/index.html.twig', ['items' => $data]);
    }

    /**
     * @param TranslatorInterface $translator
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     * @Route("/api/shop", name="api_shop", methods={"GET"})
     */
    public function getShopTrans(TranslatorInterface $translator, Request $request){
        $trans = [
            'function' => $translator->trans('function', [], null, $request->getLocale()),
            'profilCreate' => $translator->trans('profil.create', [], null, $request->getLocale()),
            'profilConsult' => $translator->trans('profil.consult', [], null, $request->getLocale()),
            'profilSearch' => $translator->trans('profil.search', [], null, $request->getLocale()),
            'messageReceive' => $translator->trans('message.receive', [], null, $request->getLocale()),
            'flowerReceive' => $translator->trans('flower.receive', [], null, $request->getLocale()),
            'messageSend' => $translator->trans('message.send', [], null, $request->getLocale()),
            'groupJoin' => $translator->trans('group.join', [], null, $request->getLocale()),
            'groupCreate' => $translator->trans('group.create', [], null , $request->getLocale()),
            'flowerSend' => $translator->trans('flower.send', [], null, $request->getLocale()),
            'favoriteList' => $translator->trans('favorite.list', [], null, $request->getLocale()),
            'profiles' => $translator->trans('profils', [], null, $request->getLocale()),
            'sub' => $translator->trans('subscribe', [], null, $request->getLocale()),
            'registered' => $translator->trans('registered', [], null, $request->getLocale()),
            'basic' => $translator->trans('basic', [], null, $request->getLocale()),
            'medium' => $translator->trans('medium', [], null, $request->getLocale()),
            'premium' => $translator->trans('premium', [], null, $request->getLocale()),
            'unlimited' => $translator->trans('unlimited', [], null, $request->getLocale()),
            'options' => $translator->trans('options', [], null, $request->getLocale()),
            'month' => $translator->trans('month', [], null, $request->getLocale())
        ];

        return $this->json($trans);
    }
}
