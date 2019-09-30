<?php

namespace App\Controller;

use App\Entity\User;
use App\Service\MailingService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

class ApiMailerController extends AbstractController
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
     * @param MailingService $mailingService
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     * @Route("/api/messages/mailer", name="api_mailer", methods={"POST"})
     */
    public function index(Request $request, MailingService $mailingService)
    {
        $data = $this->_serializer->decode($request->getContent(), 'json');
        $em = $this->getDoctrine()->getRepository(User::class);
        $target = $em->find($data['target']);
        $from = $em->find($data['from']);
        $mailingService->sendUnconnectedMail($from, $target);

        return $this->json(['success' => 'ok']);
    }
}
