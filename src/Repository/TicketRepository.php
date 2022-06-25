<?php

namespace App\Repository;

use App\Entity\Ticket;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Ticket|null find($id, $lockMode = null, $lockVersion = null)
 * @method Ticket|null findOneBy(array $criteria, array $orderBy = null)
 * @method Ticket[]    findAll()
 * @method Ticket[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TicketRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Ticket::class);
    }

    public function findAllTickets($projectId, $onlyQuery = false)
    {
        $query = $this->createQueryBuilder('t')
            ->join('t.project', 'p')
            ->where('p.id = :id')
            ->setParameter('id', $projectId)
            ->getQuery();
        if ($onlyQuery) {
            return $query;
        }
        return $query->getResult();
    }

    public function save(Ticket $ticket,$doNotFlush = false)
    {
        $this->_em->persist($ticket);
        if (!$doNotFlush) {
            $this->_em->flush();
        }
    }

}
