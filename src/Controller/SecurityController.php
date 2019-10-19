<?php

namespace App\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Contracts\Translation\TranslatorInterface;

class SecurityController extends AbstractController
{
    /**
     * @param AuthenticationUtils $authenticationUtils
     * @param TranslatorInterface $translator
     * @param Request $request
     * @return Response
     * @Route("/login", name="app_login")
     */
    public function login(AuthenticationUtils $authenticationUtils, TranslatorInterface $translator, Request $request): Response
    {
         if ($this->getUser()) {
            $this->redirectToRoute('index');
         }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error,
            'forgot' => $translator->trans('forget.password', [], null, $request->getLocale()),
            'connection' => $translator->trans('connection.link', [], null, $request->getLocale())]);
    }

    /**
     * @Route("/logout", name="app_logout")
     */
    public function logout()
    {
       return $this->redirectToRoute('index');
    }

    /**
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     * @Route("api/user", name="api_get_user", methods={"GET"})
     */
    public function getUserRoles(){
        $data = [];
        /** @var  $user User */
        $user = $this->getUser();
        $this->isGranted('ROLE_ADMIN');
        if (!$user){
            $data['isSub'] = false;
            $data['isPremium'] = false;
            return $this->json($data);
        }
        if ($this->isGranted('ROLE_PREMIUM')){
            $data['isSub'] = true;
            $data['isPremium'] = true;
            return $this->json($data);
        }
        if($this->isGranted('ROLE_BASIC') || $this->isGranted('ROLE_MEDIUM')){
            $data['isSub'] = true;
            $data['isPremium'] = false;
            return $this->json($data);
        }
        if($this->isGranted('ROLE_USER')){
            $data['isSub'] = false;
            $data['isPremium'] = false;
            return $this->json($data);
        }
        $data['isPremium'] = false;
        $data['isSub'] = false;
        return $this->json($data);
    }
}
