<?php


namespace App\Controller;

use App\Entity\User;
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

     * @return \Symfony\Component\HttpFoundation\JsonResponse
     * @throws \Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface
     * @Route("/api/register", name="app_register_form", methods={"POST"})
     */
    public function index(Request $request, UserPasswordEncoderInterface $encoder, \Swift_Mailer $mailer){
        if (!self::checkToken($request)){
            return $this->json(['data' => ['error' => 'bad token']]);
        }
        if (self::checkCountry($request)){ //TODO retirer le fr à la fin
            $token = self::genToken();
            $data = $this->_serializer->decode($request->getContent(), 'json');
            $em = $this->getDoctrine()->getManager();
            $user = new User();
            $user->setPassword($encoder->encodePassword($user, $data['credentials']['password']));
            $user->setEmail($data['credentials']['email']);
            $user->setRoles(['ROLE_USER']);
            $user->setCreatedAt(new \DateTime('now'));
            $user->setResetToken($token);
            $user->setIsValidated(false);
            $em->persist($user);
            $em->flush();

            $message = new \Swift_Message();
            $message->setSubject('Registrattion ParentSolo');
            $message->setTo($user->getEmail());
            $message->setFrom('noreply@parentsolo.ch');
            $message->setBody('
            <body>
                <h1>Confirmation for registration  to parentsolo.ch</h1>
                <p>Please click on the button to register finally</p>
                <a class="btn btn-primary btn-group" href="https://parentsolo.backndev.fr/register/' . $token . '">Register</a>
            </body>', 'text/html');
            $mailer->send($message);
            return $this->json('An email was sent to ' . $user->getEmail());
        }
        return $this->json(['data' => ['error' => 'You must be a Swissman to be registered if you use a VPN you have to deactivate it to register']]);
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
        // TODO  faire la method de mailer séparée en cas d'echec de mail .
        $user->setUpdatedAt(new \DateTime('now'));
        $user->setResetToken(null);
        $em->flush();
        return $this->redirectToRoute('app_login');
    }

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
            return false;
        }
        $client = HttpClient::create();
        $response = $client->request('GET', $this->getParameter('api.geo.uri') . '/' . $clientIp);
        $data = $this->_serializer->decode($response->getContent(), 'json');
        if ($data['countryCode'] == 'CH' || $data['countryCode'] == 'FR'){ //TODO retirer le fr à la fin
            return $data;
        }
        return false;
    }

    private static function genToken(){
        return $token = bin2hex(random_bytes(36));
    }
}