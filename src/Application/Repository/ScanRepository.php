<?php

namespace App\Repository;

use App\Entity\Scan;
use Doctrine\ORM\EntityRepository;

class ScanRepository extends EntityRepository
{
    public function findRecent(int $limit = 50): array
    {
        return $this->createQueryBuilder('s')
            ->orderBy('s.createdAt', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    public function findByDateRange(\DateTime $from, \DateTime $to): array
    {
        return $this->createQueryBuilder('s')
            ->where('s.createdAt BETWEEN :from AND :to')
            ->setParameter('from', $from)
            ->setParameter('to', $to)
            ->orderBy('s.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function findBySubmitter(string $submitter): array
    {
        return $this->findBy(['submittedBy' => $submitter], ['createdAt' => 'DESC']);
    }
}
