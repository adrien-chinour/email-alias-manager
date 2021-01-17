<?php

namespace App\Repository;

use App\Entity\AliasDiff;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method AliasDiff|null find($id, $lockMode = null, $lockVersion = null)
 * @method AliasDiff|null findOneBy(array $criteria, array $orderBy = null)
 * @method AliasDiff[]    findAll()
 * @method AliasDiff[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AliasDiffRepository extends AbstractEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AliasDiff::class);
    }

    public function reset()
    {
        foreach ($this->findAll() as $aliasDiff) {
            $this->delete($aliasDiff);
        }
    }
}
