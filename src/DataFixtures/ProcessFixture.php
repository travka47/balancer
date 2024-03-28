<?php

namespace App\DataFixtures;

use App\Entity\Process;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class ProcessFixture extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $process = new Process();
        $process->setRequiredRam(9);
        $process->setRequiredCpu(10);

        $manager->persist($process);
        $manager->flush();
    }
}
