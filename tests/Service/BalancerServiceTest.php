<?php

namespace App\Tests\Service;

use App\DataFixtures\BalancerServiceWorkstationsFixture;
use App\DataFixtures\ProcessFixture;
use App\Entity\Workstation;
use App\Repository\WorkstationRepository;
use App\Service\BalancerService;
use App\Tests\BalancerKernelTestCase;

class BalancerServiceTest extends BalancerKernelTestCase
{
    private WorkstationRepository $workstationRepository;
    private BalancerService $balancerService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->balancerService = self::getContainer()->get(BalancerService::class);
        $this->workstationRepository = self::getContainer()->get(WorkstationRepository::class);
    }

    protected function createFixtures(array $workstationsData, int $processesCount): array
    {
        return [
            new BalancerServiceWorkstationsFixture($workstationsData),
            ...array_fill(0, $processesCount, new ProcessFixture())
        ];
    }

    public function testBalancingSmallData(): void
    {
        $workstationsData = [
            [
                'ram' => 50,
                'cpu' => 50,
                'processes_count' => 5
            ],
            [
                'ram' => 20,
                'cpu' => 20,
                'processes_count' => 2
            ],
            [
                'ram' => 15,
                'cpu' => 15,
                'processes_count' => 1
            ],
        ];
        $this->executor->execute($this->createFixtures($workstationsData, 8));

        $this->balancerService->balance();

        foreach ($workstationsData as $data) {
            $this->assertCount($data['processes_count'],
                $this->workstationRepository->findOneBy(['totalRam' => $data['ram']])->getProcesses()
            );
        }
    }

    public function testBalancingBigData(): void
    {
        $workstationsData = [
            [
                'ram' => 20000,
                'cpu' => 20000,
                'processes_count' => 667
            ],
            [
                'ram' => 10000,
                'cpu' => 10000,
                'processes_count' => 333
            ],
        ];
        $this->executor->execute($this->createFixtures($workstationsData, 1000));

        $this->balancerService->balance();

        foreach ($workstationsData as $data) {
            $this->assertCount($data['processes_count'],
                $this->workstationRepository->findOneBy(['totalRam' => $data['ram']])->getProcesses()
            );
        }
    }

    public function testBalancingAfterWorkstationAdding(): void
    {
        $workstationsData = [
            [
                'ram' => 50,
                'cpu' => 50,
                'processes_count_after_workstation_adding' => 3
            ],
            [
                'ram' => 20,
                'cpu' => 20,
                'processes_count_after_workstation_adding' => 1
            ],
            [
                'ram' => 15,
                'cpu' => 15,
                'processes_count_after_workstation_adding' => 1
            ],
        ];
        $this->executor->execute($this->createFixtures($workstationsData, 8));

        $this->balancerService->balance();

        $newWorkstation = new Workstation();
        $newWorkstation->setTotalRam(40);
        $newWorkstation->setTotalCpu(40);
        $this->entityManager->persist($newWorkstation);
        $this->entityManager->flush();

        $workstationsData[] = [
            'ram' => $newWorkstation->getTotalRam(),
            'cpu' => $newWorkstation->getTotalCpu(),
            'processes_count_after_workstation_adding' => 3
        ];

        $this->balancerService->balance();

        foreach ($workstationsData as $data) {
            $this->assertCount($data['processes_count_after_workstation_adding'],
                $this->workstationRepository->findOneBy(['totalRam' => $data['ram']])->getProcesses()
            );
        }
    }
}
