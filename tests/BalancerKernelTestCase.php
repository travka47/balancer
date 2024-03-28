<?php

namespace App\Tests;

use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class BalancerKernelTestCase extends KernelTestCase
{
    protected EntityManagerInterface $entityManager;
    protected ORMExecutor $executor;

    protected function setUp(): void
    {
        parent::setUp();

        $this->entityManager = self::getContainer()->get('doctrine')->getManager();

        $this->executor = new ORMExecutor($this->entityManager, new ORMPurger($this->entityManager));
    }
}
