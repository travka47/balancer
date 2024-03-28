<?php

namespace App\Tests;

use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

abstract class BalancerWebTestCase extends WebTestCase
{
    protected EntityManagerInterface $entityManager;

    protected ORMExecutor $executor;
    protected KernelBrowser $client;

    protected function setUp(): void
    {
        parent::setUp();
        $this->client = self::createClient();

        $this->entityManager = self::getContainer()->get('doctrine')->getManager();
        $this->executor = new ORMExecutor($this->entityManager, new ORMPurger($this->entityManager));
        $this->executor->execute($this->createFixtures());
    }

    abstract protected function createFixtures(): array;
}

