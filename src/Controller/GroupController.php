<?php

namespace App\Controller;

use App\Entity\Groups;
use App\Entity\Img;
use App\Entity\Slug;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

class GroupController extends AbstractController
{
    private $_serializer;

    public function __construct()
    {
        $encoders = [new XmlEncoder(), new JsonEncoder()];
        $normalizers = [new ObjectNormalizer()];
        $this->_serializer = new Serializer($normalizers, $encoders);
    }

    /**
     * @Route("/group", name="group")
     */
    public function index()
    {
        return $this->render('group/index.html.twig', [
            'controller_name' => 'GroupController',
        ]);
    }

    /**
     * @param null $id
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     * @Route("/api/group/{id}", name="api_get_group", methods={"GET"})
     */

    public function getGroup($id = null){
        $em = $this->getDoctrine()->getRepository(Groups::class);
        if ($id){
            return $this->json($em->find($id), 200);
        }
        return $this->json($em->findAll(), 200);
    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     * @Route("/admin/api/group/create", name="admin_api_create_group", methods={"POST"})
     */
    public function createGroup(Request $request){
        $data = $this->_serializer->decode($request->getContent(), 'json');
        $em = $this->getDoctrine()->getManager();
        $group = new Groups();
        $group->setDescription($data['description']);
        $group->setName($data['name']);
        $users = [];
        foreach ($data['users'] as $user){
            array_push($users, $user);
        }
        $group->setMembers($users);
        $em->persist($group);
        $img = $em->getRepository(Img::class)->find($data['img']);
        $img->setGroups($group);
        $em->flush();

        return $this->json('ok');
    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     * @Route("/api/group/adduser", name="api_group_adduser", methods={"PUT"})
     */
    public function addUser(Request $request){
        $data = $this->_serializer->decode($request->getContent(), 'json');
        $em = $this->getDoctrine()->getManager();
        $group = $em->getRepository(Groups::class)->find($data['groupId']);
        $users = $group->getMembers();
        if (!in_array($data['userId'], $users)){
            array_push($users, $data['userId']);
            $group->setMembers($users);
            $em->flush();
            return $this->json('success');
        }

        return $this->json('fail', 500);
    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     * @Route("/admin/api/group/update", name="admin_api_group_update", methods={"PUT"})
     */
    public function updateGroup(Request $request){
        $data = $this->_serializer->decode($request->getContent(), 'json');
        $em = $this->getDoctrine()->getManager();
        $group = $em->getRepository(Groups::class)->find($data['groupId']);
        $group->setName($data['name']);
        $group->setDescription($data['description']);
        $group->setMembers($data['users']);
        $img = $em->getRepository(Img::class)->find($data['img']);
        $img->setGroups($group);
        $em->flush();

        return $this->json('success');
    }


    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     * @Route("/admin/api/group/deleteuser", name="admin_api_group_deleteuser", methods={"DELETE"})
     */
    public function deleteUser(Request $request){
        $data = $this->_serializer->decode($request->getContent(), 'json');
        $em = $this->getDoctrine()->getManager();
        $group = $em->getRepository(Groups::class)->find($data['groupId']);
        $users = $group->getMembers();
        $newUsers = [];
        foreach ($users as $user){
            if ($user != $data['userId']){
                array_push($newUsers, $user);
            }
        }
        $group->setMembers($newUsers);
        $em->flush();
        return $this->json('success');
    }

    /**
     * @param $id
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     * @Route("/admin/api/group/delete/{id}", name="admin_api_delete_group", methods={"DELETE"})
     */
    public function deleteGroup($id){
        $em = $this->getDoctrine()->getManager();
        $group = $em->getRepository(Groups::class)->find($id);
        $em->remove($group);
        return $this->json('success');
    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     * @Route("/api/group/post", name="api_group_post", methods={"POST"})
     */
    public function addSlug(Request $request){
        $data = $this->_serializer->decode($request->getContent(), 'json');
        $em = $this->getDoctrine()->getManager();
        /** @var User $user */
        $user = $this->getUser();
        $slug = new Slug();
        $slug->setText($data['text']);
        $group = $em->getRepository(Groups::class)->find($data['groupId']);
        $slug->addGroup($group);
        $slug->setAuthor($user);
        $em->persist($slug);
        $em->flush();
        return $this->json('success');
    }

    /**
     * @param $group
     * @param null $id
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     * @Route("/api/group/slug/{group}/{id}", name="api_get_slugs", methods={"GET"})
     */
    public function getSlug($group, $id = null){
        $em = $this->getDoctrine()->getRepository(Slug::class);
        if ($id){
            return $this->json($em->find($id));
        }
        $group = $this->getDoctrine()->getRepository(Groups::class)->find($group);
        $slugs = [];
        foreach ($group->getSlugs()->getValues() as $slug){
            /** @var  Slug $slug */
            $content['text'] = $slug->getText();
            $content['id'] = $slug->getId();
            $content['author'] = $slug->getAuthor();
            array_push($slugs, $content);
        }
        return $this->json($slugs);
    }
}
