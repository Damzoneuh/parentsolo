<?php

namespace App\Controller;

use App\Entity\Img;
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
        $data = [];
        $img = $user->getImg()->getValues();
        if (!empty($img[0])){
            $image = $img[0];
            /** @var $image Img */
            $data['profilImg'] = $image->getId();
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

        return $this->render('dashboard/index.html.twig', ['data' => $data]);
    }
}
