<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use DateTime;

/**
 * Scan entity representing a submitted moon scan.
 */
#[ORM\Entity]
#[ORM\Table(name: 'scans')]
class Scan
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 50)]
    private string $scanType;

    #[ORM\Column(type: 'text')]
    private string $rawData;

    #[ORM\Column(type: 'integer')]
    private int $moonCount;

    #[ORM\Column(type: 'integer')]
    private int $materialCount;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $submittedBy = null;

    #[ORM\Column(type: 'string', length: 50, nullable: true)]
    private ?string $status = null;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $errorMessage = null;

    #[ORM\Column(type: 'datetime')]
    private DateTime $createdAt;

    public function __construct(
        string $scanType,
        string $rawData,
        int $moonCount,
        int $materialCount
    ) {
        $this->scanType = $scanType;
        $this->rawData = $rawData;
        $this->moonCount = $moonCount;
        $this->materialCount = $materialCount;
        $this->status = 'processed';
        $this->createdAt = new DateTime('now');
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getScanType(): string
    {
        return $this->scanType;
    }

    public function getRawData(): string
    {
        return $this->rawData;
    }

    public function getMoonCount(): int
    {
        return $this->moonCount;
    }

    public function getMaterialCount(): int
    {
        return $this->materialCount;
    }

    public function getSubmittedBy(): ?string
    {
        return $this->submittedBy;
    }

    public function setSubmittedBy(?string $submittedBy): self
    {
        $this->submittedBy = $submittedBy;
        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;
        return $this;
    }

    public function getErrorMessage(): ?string
    {
        return $this->errorMessage;
    }

    public function setErrorMessage(?string $errorMessage): self
    {
        $this->errorMessage = $errorMessage;
        return $this;
    }

    public function getCreatedAt(): DateTime
    {
        return $this->createdAt;
    }
}
