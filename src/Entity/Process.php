<?php

namespace App\Entity;

use App\Repository\ProcessRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ProcessRepository::class)]
class Process
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(
        type: 'integer',
        nullable: false,
        options: ['unsigned' => true]
    )]
    #[Groups(['process', 'workstation_processes'])]
    private int $id;

    #[ORM\Column(
        name: 'required_ram',
        type: 'integer',
        nullable: false,
        options: ['unsigned' => true]
    )]
    #[Groups(['process', 'workstation_processes'])]
    #[Assert\NotBlank]
    #[Assert\Type(type: 'integer')]
    #[Assert\GreaterThan(value: 0)]
    private int $requiredRam;

    #[ORM\Column(
        name: 'required_cpu',
        type: 'integer',
        nullable: false,
        options: ['unsigned' => true]
    )]
    #[Groups(['process', 'workstation_processes'])]
    #[Assert\NotBlank]
    #[Assert\Type(type: 'integer')]
    #[Assert\GreaterThan(value: 0)]
    private int $requiredCpu;

    #[ORM\ManyToOne(
        targetEntity: Workstation::class,
        inversedBy: 'processes'
    )]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups('process_workstation')]
    private Workstation $workstation;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getRequiredRam(): int
    {
        return $this->requiredRam;
    }

    public function setRequiredRam(int $requiredRam): static
    {
        $this->requiredRam = $requiredRam;

        return $this;
    }

    public function getRequiredCpu(): ?int
    {
        return $this->requiredCpu;
    }

    public function setRequiredCpu(int $requiredCpu): static
    {
        $this->requiredCpu = $requiredCpu;

        return $this;
    }

    public function getWorkstation(): Workstation
    {
        return $this->workstation;
    }

    public function setWorkstation(Workstation $workstation): static
    {
        $this->workstation = $workstation;

        return $this;
    }
}