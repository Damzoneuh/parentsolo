<?php

namespace App\Controller;

use App\Entity\Childs;
use App\Entity\Cook;
use App\Entity\Hobbies;
use App\Entity\Img;
use App\Entity\Langages;
use App\Entity\Movies;
use App\Entity\Music;
use App\Entity\Outing;
use App\Entity\Pets;
use App\Entity\Reading;
use App\Entity\Sport;
use App\Entity\User;
use App\Service\ItemsService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Finder\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

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
     * @return AccessDeniedException|\Symfony\Component\HttpFoundation\JsonResponse
     * @Route("/api/favorite/{id}", name="api_put_favorite", methods={"PUT"})
     */
    public function handleFavorite($id){
        /** @var User $currentUser */
        $currentUser = $this->getUser();
        $user = $this->getDoctrine()->getRepository(User::class)->find($id);
        $em = $this->getDoctrine()->getManager();

        if (ItemsService::checkFavorite($currentUser, $user)){
            $currentUser->getProfil()->removeFavorite($user);
            $em->persist($currentUser);
            $em->flush();

            return $this->json('removed');
        }

        if ($this->isGranted('ROLE_PREMIUM') || $currentUser->getFavoriteNumber() > 0){

            $currentUser->getProfil()->addFavorite($user);
            $favoriteNumber = $currentUser->getFavoriteNumber();

            if (!$this->isGranted('ROLE_PREMIUM')){
                $currentUser->setFavoriteNumber($favoriteNumber - 1);
            }

            $em->persist($currentUser);
            $em->flush();

            return $this->json('added');
        }

        return $this->json('error', 403);
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

    /**
     * @param Request $request
     * @param TranslatorInterface $translator
     * @param null $id
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     * @Route("/api/profile/{id}", name="api_profile", methods={"GET"})
     */
    public function getProfile(Request $request, TranslatorInterface $translator, $id = null){
        $em = $this->getDoctrine()->getRepository(User::class);
        $result = [];
        /** @var User $currentUser */
        $currentUser = $this->getUser();
        if (!$id){
            $users = $em->excludeCurrentUser($currentUser);
            if (!$users){
                return $this->json($result, 200);
            }
            /** @var User $user */
            foreach ($users as $user){
                array_push($result, self::createFullyUserPayload($user, $currentUser, $request, $translator));
            }
            return $this->json($result, 200);
        }

        $user = $em->find($id);
        if (!$user){
            return $this->json($result, 404);
        }
        return $this->json(self::createFullyUserPayload($user, $currentUser, $request, $translator), 200);
    }


    /**
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/edit/profile", name="edit_profile")
     */
    public function renderEditProfile(){
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

        return $this->render('profil/edit.html.twig', ['data' => $data]);
    }

    private static function createFullyUserPayload(User $user, User $currentUser, Request $request, TranslatorInterface $translator){
        $data = [];

        $langs = $user->getProfil()->getLangages();
        $data['personality']['lang'] = [];
        if ($langs->count() == 0){
            $data['personality']['lang'] = null;
        }
        else{
            /** @var Langages $lang */
            foreach ($langs->getValues() as $lang){
                array_push($data['personality']['lang'],
                    [
                        'id' => $lang->getId(),
                        'lang' => $translator->trans($lang->getName(), [], null, $request->getLocale())]);
            }
        }

        $outings = $user->getProfil()->getOuting();
        $data['outings'] = [];
        if ($outings->count() == 0){
            $data['outings'] = null;
        }
        else{
            /** @var Outing $outing */
            foreach ($outings->getValues() as $outing){
                array_push($data['outings'], [
                    'id' => $outing->getId(),
                    'outing' => $translator->trans($outing->getName(), [], null, $request->getLocale())
                ]);
            }
        }

        $cooks = $user->getProfil()->getCook();
        $data['cook'] = [];
        if ($cooks->count() == 0){
            $data['cook'] = null;
        }
        else{
            /** @var Cook $cook */
            foreach ($cooks->getValues() as $cook){
                array_push($data['cook'], [
                    'id' => $cook->getId(),
                    'cook' => $translator->trans($cook->getName(), [], null, $request->getLocale())
                ]);
            }
        }

        $hobbies = $user->getProfil()->getHobbies();
        $data['hobbies'] = [];
        if ($hobbies->count() == 0){
            $data['hobbies'] = null;
        }
        else{
            /** @var Hobbies $hobby */
            foreach ($hobbies->getValues() as $hobby){
                array_push($data['hobbies'], [
                    'id' => $hobby->getId(),
                    'hobby' => $translator->trans($hobby->getName(), [], null, $request->getLocale())
                ]);
            }
        }

        $sports = $user->getProfil()->getSports();
        $data['sport'] = [];
        if ($sports->count() == 0){
            $data['sport'] = null;
        }
        else{
            /** @var Sport $sport */
            foreach ($sports->getValues() as $sport){
                array_push($data['sport'], [
                    'id' => $sport->getId(),
                    'name' => $translator->trans($sport->getName(), [], null, $request->getLocale())
                ]);
            }
        }

        $musics = $user->getProfil()->getMusic();
        $data['music'] = [];
        if ($musics->count() == 0){
            $data['music'] = null;
        }
        else{
            /** @var Music $music */
            foreach ($musics as $music){
                array_push($data['music'], [
                    'id' => $music->getId(),
                    'music' => $translator->trans($music->getName(), [], null, $request->getLocale())
                ]);
            }
        }

        $movies = $user->getProfil()->getMovies();
        $data['movie'] = [];
        if ($movies->count() == 0){
            $data['movie'] = null;
        }
        else{
            /** @var Movies $movie */
            foreach ($movies->getValues() as $movie){
                array_push($data['movie'], [
                    'id' => $movie->getId(),
                    'movie' => $translator->trans($movie->getName(), [], null, $request->getLocale())
                ]);
            }
        }

        $reads = $user->getProfil()->getReading();
        $data['read'] = [];
        if ($reads->count() == 0){
            $data['read'] = null;
        }
        else{
            /** @var Reading $read */
            foreach ($reads->getValues() as $read){
                array_push($data['read'], [
                    'id' => $read->getId(),
                    'read' => $translator->trans($read->getName(), [], null, $request->getLocale())
                ]);
            }
        }

        $pets = $user->getProfil()->getPets();
        $data['pet'] = [];
        if ($pets->count() == 0){
            $data['pet'] = null;
        }
        else{
            /** @var Pets $pet */
            foreach ($pets->getValues() as $pet){
                array_push($data['pet'], [
                    'id' => $pet->getId(),
                    'pet' => $translator->trans($pet->getName(), [], null, $request->getLocale())
                ]);
            }
        }

        $imgs = $user->getImg();
        $data['img'] = [];
        if ($imgs->count() == 0){
            $data['img'] = null;
        }
        else{
            /** @var Img $img */
            foreach ($imgs->getValues() as $img){
                array_push($data['img'], [
                    'img' => $img->getId(),
                    'isProfile' => $img->getIsProfile()
                ]);
            }
        }

        $childs = $user->getProfil()->getChilds();
        $data['child'] = [];
        if ($childs->count() == 0){
            $data['child'] = null;
        }
        else{
            /** @var Childs $child */
            foreach ($childs->getValues() as $child){
                $image = [];
                $age = self::getAge($child->getBorn());
                $sex = $child->getSex();
                $imgs = $child->getImg();
                if ($imgs->count() == 0){
                    $image = null;
                }
                else{
                    foreach ($imgs->getValues() as $img){
                        array_push($image, ['id' => $img->getId()]);
                    }
                }
                array_push($data['child'], [
                    'age' => $age,
                    'sex' => $sex,
                    'img' => $image
                ]);
            }
        }

        $data['pseudo'] = $user->getPseudo();
        $data['age'] = self::getAge($user->getBirthdate()) . ' ' . $translator->trans('years.old', [], null, $request->getLocale());

        $relation = $user->getProfil()->getRelation();
        if (!$relation){
            $data['personality']['relation'] = null;
        }
        else{
            $data['personality']['relation'] = $translator->trans($user->getProfil()->getRelation()->getName(), [], null, $request->getLocale());
        }

        $familyStatus = $user->getProfil()->getFamilyStatus();
        if (!$familyStatus){
            $data['personality']['familyStatus'] = null;
        }
        else{
            $data['personality']['familyStatus'] = $translator->trans($familyStatus->getName(), [], null, $request->getLocale());
        }

        $activity = $user->getProfil()->getActivity();
        if (!$activity){
            $data['lifeStyle']['activity'] = null;
        }
        else{
            $data['lifeStyle']['activity'] = $translator->trans($activity->getName(), [], null, $request->getLocale());
        }

        $temperament = $user->getProfil()->getTemperament();
        if (!$temperament){
            $data['personality']['temperament'] = null;
        }
        else{
            $data['personality']['temperament'] = $translator->trans($user->getProfil()->getTemperament()->getName(), [], null, $request->getLocale());
        }

        $nationality = $user->getProfil()->getNationality();
        if (!$nationality){
            $data['personality']['nationality'] = null;
        }
        else{
            $data['personality']['nationality'] = $translator->trans($user->getProfil()->getNationality()->getName(), [], null, $request->getLocale());
        }

        $lifestyle = $user->getProfil()->getLifestyle();
        if (!$lifestyle){
            $data['lifeStyle']['lifeStyle'] = null;
        }
        else{
            $data['lifeStyle']['lifeStyle'] = $translator->trans($user->getProfil()->getLifestyle()->getName(), [], null, $request->getLocale());
        }

        $childGuard = $user->getProfil()->getChildGard();
        if (!$childGuard){
            $data['lifeStyle']['childGuard'] = null;
        }
        else{
            $data['lifeStyle']['childGuard'] = $translator->trans($user->getProfil()->getChildGard()->getName(), [], null, $request->getLocale());
        }

        $religion = $user->getProfil()->getReligion();
        if (!$religion){
            $data['lifeStyle']['religion'] = null;
        }
        else{
            $data['lifeStyle']['religion'] = $translator->trans($user->getProfil()->getReligion()->getName(), [], null, $request->getLocale());
        }

        $smoke = $user->getProfil()->getSmoke();
        if (!$smoke){
            $data['lifeStyle']['smoke'] = null;
        }
        else{
            $data['lifeStyle']['smoke'] = $translator->trans($user->getProfil()->getSmoke()->getName(), [], null, $request->getLocale());
        }

        $studies = $user->getProfil()->getStudies();
        if (!$studies){
            $data['lifeStyle']['studies'] = null;
        }
        else{
            $data['lifeStyle']['studies'] = $translator->trans($user->getProfil()->getStudies()->getName(), [], null, $request->getLocale());
        }

        $eyes = $user->getProfil()->getEyes();
        if (!$eyes){
            $data['appearance']['eyes'] = null;
        }
        else{
            $data['appearance']['eyes'] = $translator->trans($user->getProfil()->getEyes()->getName(), [], null, $request->getLocale());
        }

        $hair = $user->getProfil()->getHair();
        if (!$hair){
            $data['appearance']['hair'] = null;
        }
        else{
            $data['appearance']['hair'] = $translator->trans($user->getProfil()->getHair()->getName(), [], null, $request->getLocale());
        }

        $hairStyle = $user->getProfil()->getHairStyle();
        if (!$hairStyle){
            $data['appearance']['hairStyle'] = null;
        }
        else{
            $data['appearance']['hairStyle'] = $translator->trans($user->getProfil()->getHairStyle()->getName(), [], null, $request->getLocale());
        }

        $silhouette = $user->getProfil()->getSilhouette();
        if (!$silhouette){
            $data['appearance']['silhouette'] = null;
        }
        else{
            $data['appearance']['silhouette'] = $translator->trans($user->getProfil()->getSilhouette()->getName(), [], null, $request->getLocale());
        }

        $origin = $user->getProfil()->getorigin();
        if (!$origin){
            $data['appearance']['origin'] = null;
        }
        else{
            $data['appearance']['origin'] = $translator->trans($user->getProfil()->getorigin()->getName(), [], null, $request->getLocale());
        }

        $favorites = $currentUser->getProfil()->getFavorite();
        if ($favorites->count() == 0){
            $data['isFavorite'] = false;
        }
        else{
            /** @var User $favorite */
            foreach ($favorites->getValues() as $favorite){
                if ($favorite->getId() == $user->getId()){
                    $data['isFavorite'] = true;
                }
            }
            if (!isset($data['isFavorite'])){
                $data['isFavorite'] = false;
            }
        }

        $data['id'] = $user->getId();
        $data['personality']['wantedChild'] = $user->getProfil()->getChildWanted();
        $data['appearance']['size'] = $user->getProfil()->getSize();
        $data['isMan'] = $user->getProfil()->getIsMan();
        $data['canton'] = $user->getProfil()->getCity()->getCanton()->getName();
        $data['city'] = $user->getProfil()->getCity()->getName();
        $data['description'] = $user->getProfil()->getDescription();

        return $data;
    }

//    /**
//     * @Route("/api/test/fav", name="api_test_fav")
//     */
//    public function test(){
//        /** @var User $me */
//        $me = $this->getUser();
//        $em = $this->getDoctrine()->getManager();
//        $jsp = $this->getDoctrine()->getRepository(User::class)->find(2);
//        $me->getProfil()->addFavorite($jsp);
//        $em->persist($me);
//        $em->flush();
//        return $this->json('ok');
//    }



    private static function getAge($birthDate){
        $date = new \DateTime('now');
        $age = $date->diff($birthDate);
        return $age->y;
    }
}
