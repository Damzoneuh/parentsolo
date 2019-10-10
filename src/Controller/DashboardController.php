<?php

namespace App\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AbstractController
{
    /**
     * @Route("/dashboard", name="dashboard")
     */
    public function index()
    {
        /** @var $user User */
        $user = $this->getUser();
        $display = true;
        if (in_array($user->getRoles(), ['ROLE_BASIC']) || in_array($user->getRoles(), ['ROLE_MEDIUM']) || in_array($user->getRoles(), ['ROLE_PREMIUM'])){
            $display = false;
        }
        $data = [];
        $img = $user->getImg()->getValues();
        if (!empty($img[0])){
            $data['profilImg'] = $img[0];
        }
        else{
            $data['profilImg'] = null;
        }
        $data['isMan'] = $user->getProfil()->getIsMan();
        $profil = $user->getProfil();
        $data['complete'] = true;

        if (empty($profil->getDescription()) || empty($profil->getActivity()->getValues())
        || empty($profil->getCook()->getValues()) || empty($profil->getEyes())
        || empty($profil->getHair()) || empty($profil->getHairStyle()) || empty($profil->getHobbies()->getValues()) || empty($profil->getLangages()->getValues())
        || empty($profil->getLifestyle()) || empty($profil->getMusic()->getValues()) || empty($profil->getNationality()) || empty($profil->getOrigin())
        || empty($profil->getOuting()->getValues()) || empty($profil->getReading()->getValues())
        || empty($profil->getSilhouette()) || empty($profil->getSize()) || empty($profil->getSmoke())){
            $data['complete'] = false;
        }

        $data['userId'] = $user->getId();

        return $this->render('dashboard/index.html.twig', ['display' => $display, $data]);
    }
}
