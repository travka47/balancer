<?php

namespace App\Service;

use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Workstation;

class WorkstationService
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function deployWorkstation(Workstation $workstation): void
    {
        $this->entityManager->persist($workstation);
        $this->entityManager->flush();
    }

    public function killWorkstation(Workstation $workstation): void
    {
        $this->entityManager->remove($workstation);
        $this->entityManager->flush();
    }
}
