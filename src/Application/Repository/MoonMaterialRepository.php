<?php

namespace App\Repository;

use App\Entity\MoonMaterial;
use Doctrine\ORM\EntityRepository;
use App\Entity\Moon;

class MoonMaterialRepository extends EntityRepository
{
    public function findByMoon(Moon $moon): array
    {
        return $this->findBy(['moon' => $moon], ['scannedAt' => 'DESC']);
    }

    public function findRecentByMoon(Moon $moon, int $days = 30): array
    {
        $since = new \DateTime("-{$days} days");
        return $this->createQueryBuilder('mm')
            ->where('mm.moon = :moon')
            ->andWhere('mm.scannedAt >= :since')
            ->setParameter('moon', $moon)
            ->setParameter('since', $since)
            ->orderBy('mm.scannedAt', 'DESC')
            ->getQuery()
            ->getResult();
    }
}
