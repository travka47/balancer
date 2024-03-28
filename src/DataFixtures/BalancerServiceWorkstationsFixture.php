<?php

namespace App\DataFixtures;

use App\Entity\Workstation;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class BalancerServiceWorkstationsFixture extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $parameters = [
            [
                'ram' => 30,
                'cpu' => 30
            ],
            [
                'ram' => 20,
                'cpu' => 20
            ],
            [
                'ram' => 10,
                'cpu' => 10
            ],
        ];

        foreach ($parameters as $wsData) {
            $workstation = new Workstation();
            $workstation->setTotalRam($wsData['ram']);
            $workstation->setTotalCpu($wsData['cpu']);

            $manager->persist($workstation);
            $manager->flush();
        }
    }
}