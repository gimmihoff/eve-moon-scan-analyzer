<?php

namespace App\Repository;

use App\Entity\Moon;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query\ResultSetMapping;

class MoonRepository extends EntityRepository
{
    public function findByEveId(int $eveId): ?Moon
    {
        return $this->findOneBy(['eveId' => $eveId]);
    }

    public function findBySystemId(int $systemId): array
    {
        return $this->findBy(['systemId' => $systemId]);
    }

    public function findByScanDate(\DateTime $since): array
    {
        return $this->createQueryBuilder('m')
            ->where('m.lastScannedAt >= :since')
            ->setParameter('since', $since)
            ->orderBy('m.lastScannedAt', 'DESC')
            ->getQuery()
            ->getResult();
    }
}
