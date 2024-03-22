<?php

namespace App\Service;

use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Process;
use App\Entity\Workstation;

class WorkstationService
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function getFreeWorkstation(Process $process): ?Workstation
    {
        return $this->entityManager->createQuery(
            'SELECT w
            FROM App\Entity\Workstation w
            JOIN App\Entity\WorkstationResource wr WITH wr.workstation = w
            WHERE wr.freeRam >= :requiredRam 
            AND wr.freeCpu >= :requiredCpu'
        )->setMaxResults(1)
            ->setParameters([
                'requiredRam' => $process->getRequiredRam(),
                'requiredCpu' => $process->getRequiredCpu(),
            ])->getOneOrNullResult();
    }
}
