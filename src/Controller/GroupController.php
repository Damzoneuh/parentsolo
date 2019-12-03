<?php

namespace App\Controller;

use App\Entity\Groups;
use App\Entity\Img;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Contracts\Translation\TranslatorInterface;

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
     * @param Request $request
     * @param TranslatorInterface $translator
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     * @Route("/api/group/trans", name="api_trans_group", methods={"GET"})
     */
    public function transGroup(Request $request, TranslatorInterface $translator){
        $data = [
            'group' => $translator->trans('groups', [], null, $request->getLocale()),
            'groupDescribe' => $translator->trans('groups.describe', [], null, $request->getLocale()),
            'showLink' => $translator->trans('groups.show.link', [], null, $request->getLocale()),
            'createLink' => $translator->trans('groups.create.link', [], null, $request->getLocale()),
            'createdBy' => $translator->trans('created.by', [], null, $request->getLocale()),
            'lastGroupLink' => $translator->trans('groups.last.link', [], null, $request->getLocale())
        ];

        return $this->json($data, 200);
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
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     * @Route("/api/last/group", name="api_last_group", methods={"GET"})
     */
    public function getLastGroup(){
        $em = $this->getDoctrine();
        $groups = $em->getRepository(Groups::class)->getLastGroup();
        $data = [];
        /** @var Groups $group */
        foreach ($groups as $group){
            $img = $em->getRepository(Img::class)->findOneBy(['groups' => $group]);
            $data['id'] = $group->getId();
            $data['name'] = $group->getName();
            $data['createdBy'] = $group->getCreatedBy();
            $data['img'] = $img->getId();
        }
        return $this->json($data, 200);
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
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     * @Route("/api/group/user/{id}", name="api_group_user", methods={"GET"})
     */
    public function getUserGroup($id){
        $user = $this->getDoctrine()->getRepository(User::class)->find($id);
        $groups = $user->getGroupsMembers()->getValues();
        $data = [];
        if (count($groups) > 0){
            foreach ($groups as $group){
                /** @var Groups $group */
                $row = [
                    'id' => $group->getId(),
                    'name' => $group->getName()
                ];
                array_push($data, $row);
            }
        }
        return $this->json($data, 200);
    }
}
