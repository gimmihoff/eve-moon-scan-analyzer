<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use DateTime;

/**
 * Moon entity representing a celestial moon in Eve Online.
 */
#[ORM\Entity]
#[ORM\Table(name: 'moons')]
class Moon
{
    #[ORM\Id]
    #[ORM\Column(type: 'integer')]
    private int $eveId;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $name = null;

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $systemId = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $systemName = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $planetName = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $moonIndex = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $ownedBy = null;

    #[ORM\Column(type: 'string', length: 500, nullable: true)]
    private ?string $notes = null;

    #[ORM\Column(type: 'datetime')]
    private DateTime $createdAt;

    #[ORM\Column(type: 'datetime')]
    private DateTime $updatedAt;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?DateTime $lastScannedAt = null;

    public function __construct(int $eveId)
    {
        $this->eveId = $eveId;
        $this->createdAt = new DateTime('now');
        $this->updatedAt = new DateTime('now');
    }

    public function getEveId(): int
    {
        return $this->eveId;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): self
    {
        $this->name = $name;
        $this->updatedAt = new DateTime('now');
        return $this;
    }

    public function getSystemId(): ?int
    {
        return $this->systemId;
    }

    public function setSystemId(?int $systemId): self
    {
        $this->systemId = $systemId;
        return $this;
    }

    public function getSystemName(): ?string
    {
        return $this->systemName;
    }

    public function setSystemName(?string $systemName): self
    {
        $this->systemName = $systemName;
        return $this;
    }

    public function getPlanetName(): ?string
    {
        return $this->planetName;
    }

    public function setPlanetName(?string $planetName): self
    {
        $this->planetName = $planetName;
        return $this;
    }

    public function getMoonIndex(): ?string
    {
        return $this->moonIndex;
    }

    public function setMoonIndex(?string $moonIndex): self
    {
        $this->moonIndex = $moonIndex;
        return $this;
    }

    public function getOwnedBy(): ?string
    {
        return $this->ownedBy;
    }

    public function setOwnedBy(?string $ownedBy): self
    {
        $this->ownedBy = $ownedBy;
        return $this;
    }

    public function getNotes(): ?string
    {
        return $this->notes;
    }

    public function setNotes(?string $notes): self
    {
        $this->notes = $notes;
        return $this;
    }

    public function getLastScannedAt(): ?DateTime
    {
        return $this->lastScannedAt;
    }

    public function setLastScannedAt(?DateTime $lastScannedAt): self
    {
        $this->lastScannedAt = $lastScannedAt;
        return $this;
    }

    public function getCreatedAt(): DateTime
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): DateTime
    {
        return $this->updatedAt;
    }
}
