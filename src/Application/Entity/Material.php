<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use DateTime;

/**
 * Material entity representing a material found in a moon scan.
 */
#[ORM\Entity]
#[ORM\Table(name: 'materials')]
class Material
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'integer')]
    private int $eveTypeId;

    #[ORM\Column(type: 'string', length: 255)]
    private string $name;

    #[ORM\Column(type: 'string', length: 50, nullable: true)]
    private ?string $category = null;

    #[ORM\Column(type: 'float', nullable: true)]
    private ?float $currentPrice = null;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?DateTime $priceUpdatedAt = null;

    #[ORM\Column(type: 'datetime')]
    private DateTime $createdAt;

    #[ORM\Column(type: 'datetime')]
    private DateTime $updatedAt;

    public function __construct(int $eveTypeId, string $name)
    {
        $this->eveTypeId = $eveTypeId;
        $this->name = $name;
        $this->createdAt = new DateTime('now');
        $this->updatedAt = new DateTime('now');
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEveTypeId(): int
    {
        return $this->eveTypeId;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getCategory(): ?string
    {
        return $this->category;
    }

    public function setCategory(?string $category): self
    {
        $this->category = $category;
        return $this;
    }

    public function getCurrentPrice(): ?float
    {
        return $this->currentPrice;
    }

    public function setCurrentPrice(?float $currentPrice): self
    {
        $this->currentPrice = $currentPrice;
        $this->priceUpdatedAt = new DateTime('now');
        $this->updatedAt = new DateTime('now');
        return $this;
    }

    public function getPriceUpdatedAt(): ?DateTime
    {
        return $this->priceUpdatedAt;
    }
}
