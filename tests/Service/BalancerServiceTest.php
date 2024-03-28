<?php

namespace App\Tests\Service;

use App\DataFixtures\BalancerServiceWorkstationsFixture;
use App\DataFixtures\ProcessFixture;
use App\Entity\Process;
use App\Entity\Workstation;
use App\Repository\ProcessRepository;
use App\Repository\WorkstationRepository;
use App\Service\BalancerService;
use App\Service\ProcessService;
use App\Service\WorkstationResourceService;
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class BalancerServiceTest extends KernelTestCase
{
    protected EntityManagerInterface $entityManager;
    private WorkstationRepository $workstationRepository;
    private ProcessRepository $processRepository;
    private ProcessService $processService;
    private WorkstationResourceService $resourceService;
    private BalancerService $balancerService;
    protected ORMExecutor $executor;

    protected function setUp(): void
    {
        parent::setUp();
        $this->entityManager = self::getContainer()->get('doctrine')->getManager();

        $this->balancerService = self::getContainer()->get(BalancerService::class);
        $this->workstationRepository = self::getContainer()->get(WorkstationRepository::class);

        $this->executor = new ORMExecutor($this->entityManager, new ORMPurger($this->entityManager));
        $this->executor->execute($this->createFixtures());
    }

    protected function createFixtures()
    {
        return [new BalancerServiceWorkstationsFixture(), ...array_fill(0, 4, new ProcessFixture())];
    }


    public function testFirst(): void
    {
        $ws = $this->entityManager->getRepository(Workstation::class)->findAll();
        $this->assertCount(3, $ws);
        $this->assertTrue(true);
    }

    public function testBalancing()
    {
        $this->balancerService->balance();

        $distributionByRam = [
            30 => 2,
            20 => 1,
            10 => 1
        ];

        foreach ($distributionByRam as $ram => $count) {
//            var_dump([$ram => count($this->entityManager->getRepository(Workstation::class)->findOneBy(['totalRam' => $ram])->getProcesses())]);
            $this->assertCount($count, $this->workstationRepository->findOneBy(['totalRam' => $ram])->getProcesses());
        }
    }

//    public function testBalancingProcessesSuccessfully(): void
//    {
//        $processes = [];
//        for ($i = 0; $i < 5; $i++) {
//            $process = new Process();
//            $process->setRequiredRam(512);
//            $process->setRequiredCpu(2);
//            $processes[] = $process;
//
//            $this->entityManager->persist($process);
//        }
//        $this->entityManager->flush();
//
//        $this->balancerService->balance();
//
//        $this->processRepository->expects($this->once())
//            ->method('findAllSortedByRamAndCpuDesc')
//            ->willReturn($processes);
//
//        foreach ($processes as $process) {
//            $this->workstationRepository->expects($this->once())
//                ->method('findFreeWorkstation')
//                ->with($process)
//                ->willReturn(new Workstation()); // Возвращаем фиктивную рабочую станцию для каждого процесса
//
//            $this->processService->expects($this->once())
//                ->method('deployProcess')
//                ->with($process, $this->isInstanceOf(Workstation::class)); // Проверяем, что метод deployProcess вызывается с верными аргументами
//        }
//    }
}
