<?php

namespace App\Controller;

use App\Entity\Img;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class ProfilController extends AbstractController
{
    /**
     * @Route("/profil", name="profil")
     */
    public function index()
    {
        return $this->render('profil/index.html.twig');
    }


    /**
     * @param int|null $limit
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     * @Route("/api/last/profile/{limit}", name="api_last_profile", methods={"GET"})
     */
    public function getLastProfile(int $limit = null){
        $em = $this->getDoctrine()->getRepository(User::class);
        $result = [];
        if (!$limit){
            $profiles = $em->findBy(['isConfirmed' => true, 'isValidated' => true], ['createdAt' => 'DESC']);
            foreach ($profiles as $profile){
                array_push($result, [
                    'id' => $profile->getId()
                ]);
            }
            return $this->json($result, 200);
        }
        $profiles = $em->findBy(['isConfirmed' => true, 'isValidated' => true], ['createdAt' => 'DESC'], $limit);
        foreach ($profiles as $profile){
            array_push($result, [
                'id' => $profile->getId()
            ]);
        }
        return $this->json($result, 200);
    }


    /**
     * @param $id
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     * @throws \Exception
     * @Route("/api/profile/light/{id}", name="api_light_profile", methods={"GET"})
     */
    public function getLastProfilesInformation($id){
        $user = $this->getDoctrine()->getRepository(User::class)->find($id);
        $data = [];
        $date = new \DateTime('now');
        $age = $date->diff($user->getBirthdate())->y;
        if ($user->getImg()->count() > 0){
            dump($user->getImg()->count());
            $imgs = $user->getImg()->getValues();
            /** @var Img $img */
            foreach ($imgs as $img){
                if ($img->getIsProfile()){
                    $data['img'] = $img->getId();
                }
            }
        }
        else{
            $data['img'] = null;
        }
        $data['age'] = $age;
        $data['canton'] = $user->getProfil()->getCity()->getCanton()->getName();
        $data['pseudo'] = $user->getPseudo();
        $data['isMan'] = $user->getProfil()->getIsMan();
        $data['id'] = $user->getId();

        return $this->json($data, 200);
    }
}
