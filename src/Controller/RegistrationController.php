<?php


namespace App\Controller;

use App\Entity\Canton;
use App\Entity\Cities;
use App\Entity\Profil;
use App\Entity\Relationship;
use App\Entity\User;
use App\Mailer\Mailing;
use App\Service\MailingService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
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
     * @param UserPasswordEncoderInterface $encoder
     * @param MailingService $mailingService
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     * @throws \Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface
     * @throws \Exception
     * @Route("/api/register", name="app_register_form", methods={"POST"})
     */
    public function index(Request $request, UserPasswordEncoderInterface $encoder, MailingService $mailingService){
        if (!self::checkToken($request)){
            return $this->json(['data' => ['error' => 'bad token']]);
        }
        $country = self::checkCountry($request);
        if ($country){
            $token = self::genToken();
            $data = $this->_serializer->decode($request->getContent(), 'json');
            $profil = new Profil();
            $profil->setIsMan($data['credentials']['sex']);
            $relationship = $this->getDoctrine()->getRepository(Relationship::class)
                ->findOneBy(['name' => $data['credentials']['relation']]);
            $profil->setRelation($relationship);
            $em = $this->getDoctrine()->getManager();

            $birthDate = new \DateTime($data['credentials']['day'] . '-' . $data['credentials']['month'] . '-' . $data['credentials']['year']);
            $city = $em->getRepository(Cities::class)->find($data['credentials']['city']);
            $user = new User();
            $user->setBirthdate($birthDate);
            $profil->setCity($city);
            $user->setCountry($country);
            $user->setProfil($profil);
            $user->setPhone($data['credentials']['number']);
            $user->setPassword($encoder->encodePassword($user, $data['credentials']['password']));
            $user->setEmail($data['credentials']['email']);
            $user->setRoles(['ROLE_USER']);
            $user->setCreatedAt(new \DateTime('now'));
            $user->setResetToken($token);
            $user->setIsValidated(false);
            $user->setIsConfirmed(false);
            $em->persist($user);
            $em->flush();
            $mailingService->sendRegistrationConfirmationMail($user, $token);
        }
        return $this->json(['data' => 'success']);
    }

    /**
     * @param $id
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @throws \Exception
     * @Route("/register/{id}", name="app_register", methods={"GET"})
     */
    public function registerAfterMail($id){
        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository(User::class)->findOneBy(['resetToken' => $id]);
        $user->setUpdatedAt(new \DateTime('now'));
        $user->setResetToken(null);
        $em->flush();
        return $this->redirectToRoute('app_login');
    }

    /**
     * @param Request $request
     * @param \Swift_Mailer $mailer
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     * @throws \Exception
     * @Route("/reset", name="user_reset", methods={"POST"})
     */
    public function resetPassword(Request $request, \Swift_Mailer $mailer){
        $data = $this->_serializer->decode($request->getContent(), 'json');
        if (!self::checkToken($request)){
            return $this->json(['error' => 'Bad protection token']);
        }
        $token = self::genToken();
        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository(User::class)->findOneBy(['email' => $data['email']]);
        if ($user){
            $user->setResetToken($token);
            $em->flush();
            $mail = new Mailing($mailer);
            $mail->sendResetMessage($user->getEmail(), $token);
            return $this->json(['success' => 'An email was sent to ' . $user->getEmail()], 200);
        }
        return $this->json(['error' => 'user not found']);
    }

    /**
     * @param Request $request
     * @param UserPasswordEncoderInterface $encoder
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     * @Route("/api/reset", name="api_user_reset_mail", methods={"PUT"})
     */
    public function setNewPassword(Request $request, UserPasswordEncoderInterface $encoder){
        $data = $this->_serializer->decode($request->getContent(), 'json');
        if (self::checkToken($request))
        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository(User::class)->findOneBy(['resetToken' => $data['resetToken']]);
        if (!$user){
            return $this->json(['error' => 'User not found']);
        }
        $user->setPassword($encoder->encodePassword($user, $data['plainPassword']));
        $em->flush();

        return $this->json(['success' => 'Your password as been reset']);
    }

    /**
     * @param $token
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/reset/browse/{token}", name="user_reset_render")
     */
    public function renderReset($token){
        return $this->render('security/reseting.html.twig', ['token' => $token]);
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/reset/email", name="user_reset_mail")
     */
    public function renderEmailCheck(){
        return $this->render('security/resetEmail.html.twig');
    }

    /**
     * @param Request $request
     * @return bool
     */
    private function checkToken(Request $request){
        $content = $request->getContent();
        $data = $this->_serializer->decode($content, 'json');
        return ($this->isCsrfTokenValid('register', $data['token'])) ? true : false;
    }

    /**
     * @param Request $request
     * @return bool|mixed
     * @throws \Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface
     */
    private function checkCountry(Request $request){
        $clientIp = $request->headers->get('x-real-ip');
        if (empty($clientIp)){
            return $data['countryCode'] = '?';
        }
        $client = HttpClient::create();
        $response = $client->request('GET', $this->getParameter('api.geo.uri') . '/' . $clientIp);
        if ($response->getStatusCode() === 200){
            $data = $this->_serializer->decode($response->getContent(), 'json');
            if ($data['countryCode'] == 'CH' || $data['countryCode'] == 'FR'){
                return $data['countryCode'];
            }
        }
        return $data['countryCode'] = '?';
    }

    /**
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     * @Route("/api/canton", name="api_get_canton", methods={"GET"})
     */
    public function getCantons(){
        $em = $this->getDoctrine()->getRepository(Canton::class);
        $data = [];
        $push = [];
        foreach ($em->findAll() as $canton){
            $push['name'] = $canton->getName();
            $push['id'] = $canton->getId();
            array_push($data, $push);
        }
        return $this->json($data);
    }

    /**
     * @param $canton
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     * @Route("/api/cities/{canton}", name="api_get_cities", methods={"GET"})
     */
    public function getCities($canton){
        $em = $this->getDoctrine()->getRepository(Canton::class);
        $cantons = $em->find($canton);
        $data = [];
        $push = [];
        foreach ($cantons->getCities() as $city){
            $push['name'] = $city->getName();
            $push['id'] = $city->getId();
            array_push($data, $push);
        }
        return $this->json($data);
    }

    /**
     * @return string
     * @throws \Exception
     */
    private static function genToken() : string {
        return $token = bin2hex(random_bytes(36));
    }
}