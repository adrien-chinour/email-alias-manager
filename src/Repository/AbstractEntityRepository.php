<?php

namespace App\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;

abstract class AbstractEntityRepository extends ServiceEntityRepository
{
    /**
     * @param object
     */
    public function save($entity)
    {
        try {
            $this->_em->persist($entity);
            $this->_em->flush();
        } catch (OptimisticLockException $ignored) {
            return;
        } catch (ORMException $ignored) {
            return;
        }
    }

    /**
     * @param object
     */
    public function delete($entity)
    {
        try {
            $this->_em->remove($entity);
            $this->_em->flush();
        } catch (OptimisticLockException $ignored) {
            return;
        } catch (ORMException $ignored) {
            return;
        }
    }
}
