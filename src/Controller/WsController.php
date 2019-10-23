<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mercure\Publisher;
use Symfony\Component\Mercure\Update;
use Symfony\Component\Routing\Annotation\Route;

class WsController
{
   public function __invoke(Publisher $publisher) : Response
   {
       $update = new Update(
           'https://parentsolo.backndev.fr/ws',
           json_encode(['status' => 'hello world'])
       );

       $publisher($update);

       return new Response('hello');
   }
}
