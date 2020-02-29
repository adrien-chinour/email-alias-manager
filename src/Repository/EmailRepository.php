<?php

namespace App\Repository;

use App\Entity\Email;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Email|null find($id, $lockMode = null, $lockVersion = null)
 * @method Email|null findOneBy(array $criteria, array $orderBy = null)
 * @method void       save(Email $entity)
 * @method void       delete(Email $entity)
 * @method Email[]    findAll()
 * @method Email[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EmailRepository extends AbstractEntityRepository
{

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Email::class);
    }

    public function getAlias($email): array
    {
        return $this->findBy(['target' => $email]);
    }

    public function search(string $search): array
    {
        $result = $this
            ->createQueryBuilder('e')
            ->where('e.alias LIKE :search')
            ->setParameter('search', "%$search%")
            ->getQuery()
            ->getResult();

        return $result ?? [];
    }
}
