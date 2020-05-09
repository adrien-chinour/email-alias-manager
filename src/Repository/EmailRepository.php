<?php

namespace App\Repository;

use App\Entity\Email;
use Doctrine\Common\Persistence\ManagerRegistry;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;

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

    private const MAX_ITEM_PER_PAGE = 10;

    /**
     * @var \Knp\Component\Pager\PaginatorInterface
     */
    private $pagination;

    public function __construct(ManagerRegistry $registry, PaginatorInterface $pagination)
    {
        parent::__construct($registry, Email::class);
        $this->pagination = $pagination;
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

    public function paginate(int $page): PaginationInterface
    {
        $query = $this->createQueryBuilder('e')->getQuery();
        return $this->pagination->paginate($query, $page, self::MAX_ITEM_PER_PAGE);
    }
}
