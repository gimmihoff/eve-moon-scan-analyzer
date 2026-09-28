<?php

namespace App\Entity;

use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use DateTime;

/**
 * MoonMaterial represents the quantity of a material on a specific moon.
 */
#[ORM\Entity]
#[ORM\Table(name: 'moon_materials')]
#[ORM\UniqueConstraint(name: 'unique_moon_material', columns: ['moon_id', 'material_id'])]
class MoonMaterial
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Moon::class)]
    #[ORM\JoinColumn(name: 'moon_id', referencedColumnName: 'eveId', nullable: false)]
    private Moon $moon;

    #[ORM\ManyToOne(targetEntity: Material::class)]
    #[ORM\JoinColumn(name: 'material_id', referencedColumnName: 'id', nullable: false)]
    private Material $material;

    #[ORM\Column(type: 'float')]
    private float $quantity;

    #[ORM\Column(type: 'float', nullable: true)]
    private ?float $totalValue = null;

    #[ORM\Column(type: 'datetime')]
    private DateTime $scannedAt;

    public function __construct(Moon $moon, Material $material, float $quantity)
    {
        $this->moon = $moon;
        $this->material = $material;
        $this->quantity = $quantity;
        $this->scannedAt = new DateTime('now');
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMoon(): Moon
    {
        return $this->moon;
    }

    public function getMaterial(): Material
    {
        return $this->material;
    }

    public function getQuantity(): float
    {
        return $this->quantity;
    }

    public function getTotalValue(): ?float
    {
        return $this->totalValue;
    }

    public function calculateTotalValue(): float
    {
        $price = $this->material->getCurrentPrice() ?? 0;
        $this->totalValue = $this->quantity * $price;
        return $this->totalValue;
    }

    public function getScannedAt(): DateTime
    {
        return $this->scannedAt;
    }
}
