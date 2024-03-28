<?php

namespace App\DataFixtures;

use App\Entity\Workstation;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class BalancerServiceWorkstationsFixture extends Fixture
{
    private array $workstationsData;

    public function __construct(array $workstationsData)
    {
        $this->workstationsData = $workstationsData;
    }

    public function load(ObjectManager $manager): void
    {
        foreach ($this->workstationsData as $data) {
            $workstation = new Workstation();
            $workstation->setTotalRam($data['ram']);
            $workstation->setTotalCpu($data['cpu']);

            $manager->persist($workstation);
        }
        $manager->flush();
    }
}