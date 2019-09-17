<?php


namespace App\Service;

use App\Entity\Items;
use App\Entity\User;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;

class ItemsService
{
    private $_em;

    public function __construct(EntityManager $entityManager)
    {
        $this->_em = $entityManager;
    }

    public function createItem($id, $userId) : bool
    {
        $item = $this->_em->getRepository(Items::class)->find($id);
        $user = $this->_em->getRepository(User::class)->find($userId);
        if (strstr($item->getType(), 'flower')){
            $user->setFlowerNumber($item->getQuantity());
        }
        if (strstr($item->getType(), 'favorite')){
            $user->setFavoriteNumber($item->getQuantity());
        }
        try {
            $this->_em->persist($user);
        } catch (ORMException $e) {
            return false;
        }
        try {
            $this->_em->flush();
        } catch (OptimisticLockException $e) {
            return false;
        }
        return true;
    }
}