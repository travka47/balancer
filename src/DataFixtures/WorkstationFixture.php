<?php

namespace App\DataFixtures;

use App\Entity\Workstation;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class WorkstationFixture extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $workstation = new Workstation();
        $workstation->setTotalRam(100);
        $workstation->setTotalCpu(100);

        $manager->persist($workstation);
        $manager->flush();
    }
}
