<?php

namespace App\Entity;

use App\Repository\WorkstationRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: WorkstationRepository::class)]
class Workstation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(
        type: 'integer',
        nullable: false,
        options: ['unsigned' => true]
    )]
    private int $id;

    #[ORM\Column(
        name: 'total_ram',
        type: 'integer',
        nullable: false,
        options: ['unsigned' => true]
    )]
    private int $totalRam;

    #[ORM\Column(
        name: 'total_cpu',
        type: 'integer',
        nullable: false,
        options: ['unsigned' => true]
    )]
    private int $totalCpu;

    #[ORM\OneToMany(
        targetEntity: Process::class,
        mappedBy: 'workstation',
        orphanRemoval: true
    )]
    private Collection $processes;

    #[ORM\OneToOne(
        targetEntity: WorkstationResource::class,
        mappedBy: 'workstation',
        cascade: ['persist', 'remove'],
        orphanRemoval: true
    )]
    private WorkstationResource $resource;

    public function __construct()
    {
        $this->processes = new ArrayCollection();
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getTotalRam(): int
    {
        return $this->totalRam;
    }

    public function setTotalRam(int $totalRam): static
    {
        $this->totalRam = $totalRam;

        return $this;
    }

    public function getTotalCpu(): int
    {
        return $this->totalCpu;
    }

    public function setTotalCpu(int $totalCpu): static
    {
        $this->totalCpu = $totalCpu;

        return $this;
    }

    /**
     * @return Collection<int, Process>
     */
    public function getProcesses(): Collection
    {
        return $this->processes;
    }

    public function addProcess(Process $process): static
    {
        if (!$this->processes->contains($process)) {
            $this->processes->add($process);
            $process->setWorkstation($this);
        }

        return $this;
    }

    public function removeProcess(Process $process): static
    {
        $this->processes->removeElement($process);

        return $this;
    }

    public function getResource(): WorkstationResource
    {
        return $this->resource;
    }

    public function setResource(WorkstationResource $resource): static
    {
        // set the owning side of the relation if necessary
        if ($resource->getWorkstation() !== $this) {
            $resource->setWorkstation($this);
        }

        $this->resource = $resource;

        return $this;
    }
}
