<?php

namespace App\Controller;

use App\Entity\Items;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class ShopController extends AbstractController
{
    /**
     * @Route("/api/shop", name="api_shop", methods={"GET"})
     */
    public function index()
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
        return $this->json($data);
    }
}
