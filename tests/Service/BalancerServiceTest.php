<?php

namespace App\Tests\Service;

use App\DataFixtures\BalancerServiceWorkstationsFixture;
use App\DataFixtures\ProcessFixture;
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

    protected function createFixtures(array $workstationsData): array
    {
        return [
            new BalancerServiceWorkstationsFixture($workstationsData),
            ...array_fill(0, 8, new ProcessFixture())
        ];
    }

    public function testBalancing(): void
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
        $this->executor->execute($this->createFixtures($workstationsData));

        $this->balancerService->balance();

        foreach ($workstationsData as $data) {
            $this->assertCount($data['processes_count'],
                $this->workstationRepository->findOneBy(['totalRam' => $data['ram']])->getProcesses()
            );
        }
    }
}
