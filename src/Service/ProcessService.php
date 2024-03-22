<?php

namespace App\Service;

use App\Entity\Workstation;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Process;

class ProcessService
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function deployProcess(Process $process, Workstation $workstation): void
    {
        $process->setWorkstation($workstation);
        $resource = $workstation->getResource();

        $resource->setFreeRam($resource->getFreeRam() - $process->getRequiredRam());
        $resource->setFreeCpu($resource->getFreeCpu() - $process->getRequiredCpu());

        $this->entityManager->persist($process);
        $this->entityManager->flush();
    }

    public function killProcess(Process $process): void
    {
        $resource = $process->getWorkstation()->getResource();

        $resource->setFreeRam($resource->getFreeRam() + $process->getRequiredRam());
        $resource->setFreeCpu($resource->getFreeCpu() + $process->getRequiredCpu());

        $this->entityManager->remove($process);
        $this->entityManager->flush();
    }
}
