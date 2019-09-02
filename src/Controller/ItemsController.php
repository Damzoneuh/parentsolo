<?php

namespace App\Controller;

use App\Entity\Items;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class ItemsController extends AbstractController
{
    /**
     * @Route("/api/items/{id}", name="api_items", methods={"GET"})
     * @param null $id
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function index($id = null)
    {
       $em = $this->getDoctrine()->getRepository(Items::class);
       if (!$id){
           $items = $em->findAll();
           $data = [];
           $itemList = [];
           foreach ($items as $item){
               $data['price'] = $item->getPrice();
               $data['type'] = $item->getType();
               $data['isSubscribe'] = $item->getIsASubscribe();
               $data['id'] = $item->getId();
               array_push($itemList, $data);
           }
           return $this->json($itemList);
       }
       $data = [];
       if (null === $item = $em->find($id)){
           return $this->json($data);
       }
       $data['type'] = $item->getType();
       $data['price'] = $item->getPrice();
       $data['isSubscribe'] = $item->getIsASubscribe();
       $data['id'] = $item->getId();
       return $this->json($data);
    }
}
