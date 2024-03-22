<?php

namespace App\Entity;

use App\Repository\WorkstationResourceRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: WorkstationResourceRepository::class)]
class WorkstationResource
{
    #[ORM\Id]
    #[ORM\OneToOne(
        targetEntity: Workstation::class,
        inversedBy: 'resource',
    )]
    #[ORM\JoinColumn(
        name: 'workstation_id',
        referencedColumnName: 'id',
        nullable: false
    )]
    private Workstation $workstation;

    #[ORM\Column(
        name: 'free_ram',
        type: 'integer',
        nullable: false,
        options: ['unsigned' => true]
    )]
    #[Groups('workstation_resource')]
    private int $freeRam;

    #[ORM\Column(
        name: 'free_cpu',
        type: 'integer',
        nullable: false,
        options: ['unsigned' => true]
    )]
    #[Groups('workstation_resource')]
    private int $freeCpu;

    public function getWorkstation(): Workstation
    {
        return $this->workstation;
    }

    public function setWorkstation(Workstation $workstation): static
    {
        $this->workstation = $workstation;

        return $this;
    }

    public function getFreeRam(): int
    {
        return $this->freeRam;
    }

    public function setFreeRam(int $freeRam): static
    {
        $this->freeRam = $freeRam;

        return $this;
    }

    public function getFreeCpu(): int
    {
        return $this->freeCpu;
    }

    public function setFreeCpu(int $freeCpu): static
    {
        $this->freeCpu = $freeCpu;

        return $this;
    }
}
