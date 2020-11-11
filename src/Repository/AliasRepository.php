<?php

namespace App\Repository;

use App\Entity\Alias;
use Doctrine\Common\Persistence\ManagerRegistry;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;

/**
 * @method Alias|null find($id, $lockMode = null, $lockVersion = null)
 * @method Alias|null findOneBy(array $criteria, array $orderBy = null)
 * @method void       save(Alias $entity)
 * @method void       delete(Alias $entity)
 * @method Alias[]    findAll()
 * @method Alias[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AliasRepository extends AbstractEntityRepository
{

    private const MAX_ITEM_PER_PAGE = 10;

    private PaginatorInterface $pagination;

    public function __construct(ManagerRegistry $registry, PaginatorInterface $pagination)
    {
        parent::__construct($registry, Alias::class);
        $this->pagination = $pagination;
    }

    public function getAlias(string $email): array
    {
        return $this->findBy(['realEmail' => $email]);
    }

    public function search(string $search): array
    {
        $result = $this
            ->createQueryBuilder('e')
            ->where('e.aliasEmail LIKE :search')
            ->setParameter('search', "%$search%")
            ->getQuery()
            ->getResult();

        return $result ?? [];
    }

    public function paginate(int $page): PaginationInterface
    {
        $query = $this->createQueryBuilder('e')->getQuery();
        return $this->pagination->paginate($query, $page, self::MAX_ITEM_PER_PAGE);
    }
}
