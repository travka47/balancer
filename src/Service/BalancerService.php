<?php

namespace App\Service;

use App\Entity\Process;
use App\Repository\ProcessRepository;
use App\Repository\WorkstationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Exception\ORMException;

class BalancerService
{
    private EntityManagerInterface $entityManager;
    private WorkstationRepository $workstationRepository;
    private ProcessRepository $processRepository;
    private ProcessService $processService;
    private WorkstationResourceService $resourceService;

    public function __construct(
        EntityManagerInterface $entityManager,
        WorkstationRepository $workstationRepository,
        ProcessRepository $processRepository,
        ProcessService $processService,
        WorkstationResourceService $resourceService,
    ) {
        $this->entityManager = $entityManager;
        $this->workstationRepository = $workstationRepository;
        $this->processRepository = $processRepository;
        $this->processService = $processService;
        $this->resourceService = $resourceService;
    }

    /**
     * @throws ORMException
     */
    public function balance(): void
    {
        $methods = [
            'findAllSortedByRamAndCpuDesc',
            'findAllSortedByRamDesc',
            'findAllSortedByCpuDesc',
        ];

        $this->entityManager->beginTransaction();
        $allProcessesDeployed = false;

        foreach ($methods as $method) {
            $this->resourceService->resetTable();

            foreach ($this->processRepository->$method() as $process) {
                $workstation = $this->workstationRepository->findFreeWorkstation($process);
                $processRef = $this->entityManager->getReference(Process::class, $process->getId());

                if (!$workstation || !$processRef) {
                    continue 2;
                }

                $this->processService->deployProcess($processRef, $workstation);
            }

            $allProcessesDeployed = true;
            break;
        }

        if ($allProcessesDeployed) {
            $this->entityManager->commit();
        } else {
            $this->entityManager->rollback();
        }

    }
}
