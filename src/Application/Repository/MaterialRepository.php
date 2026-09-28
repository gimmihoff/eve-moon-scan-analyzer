<?php

namespace App\Repository;

use App\Entity\Material;
use Doctrine\ORM\EntityRepository;

class MaterialRepository extends EntityRepository
{
    public function findByEveTypeId(int $typeId): ?Material
    {
        return $this->findOneBy(['eveTypeId' => $typeId]);
    }

    public function findByName(string $name): ?Material
    {
        return $this->findOneBy(['name' => $name]);
    }

    public function findByCategory(string $category): array
    {
        return $this->findBy(['category' => $category]);
    }
}
