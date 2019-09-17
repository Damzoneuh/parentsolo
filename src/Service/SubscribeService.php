<?php


namespace App\Service;


use App\Entity\Items;
use App\Entity\Subscribe;
use App\Entity\User;
use Doctrine\ORM\EntityManager;

class SubscribeService
{
    private $_em;

    public function __construct(EntityManager $entityManager)
    {
        $this->_em = $entityManager;
    }

    public function setPayPalSubscribe(User $user, $id, $itemId){
        $em = $this->_em;
        $subscribe = new Subscribe();
        $subscribe->setPlan($id);
        $item = $em->getRepository(Items::class)->find($itemId);
        $subscribe->setItem($item);
        $deadline = new \DateTime('+' . $item->getDuration() . 'month');
        $subscribe->setDeadline($deadline);
        $subscribe->setIsAuthorized(true);
        $this->_em->persist($subscribe);
        $user->setRoles(['ROLE_USER', 'ROLE_' . $item->getRole()]);
        $user->setSubscribe($subscribe);
        $user->setUpdatedAt(new \DateTime('now'));
        $this->_em->persist($user);
        $this->_em->flush();
        return true;
    }
}