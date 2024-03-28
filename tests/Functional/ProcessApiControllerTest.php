<?php

namespace App\Tests\Functional;

use App\DataFixtures\WorkstationFixture;
use App\Entity\Process;
use App\Tests\BalancerWebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ProcessApiControllerTest extends BalancerWebTestCase
{
    protected function createFixtures(): array
    {
        return [new WorkstationFixture()];
    }

    public function testCreate(): void
    {
        $this->client->request(Request::METHOD_POST, '/api/process', [], [], [], json_encode([
            'requiredRam' => 10,
            'requiredCpu' => 20,
        ], JSON_THROW_ON_ERROR));
        self::assertResponseStatusCodeSame(Response::HTTP_CREATED);

        $process = json_decode($this->client->getResponse()->getContent(), false, 512, JSON_THROW_ON_ERROR);

        self::assertObjectHasProperty('id', $process);
        self::assertObjectHasProperty('requiredRam', $process);
        self::assertObjectHasProperty('requiredCpu', $process);

        self::assertEquals(10, $process->requiredRam);
        self::assertEquals(20, $process->requiredCpu);
    }

    public function testDelete(): void
    {
        $this->client->request(Request::METHOD_POST, '/api/process', [], [], [], json_encode([
            'requiredRam' => 10,
            'requiredCpu' => 20,
        ], JSON_THROW_ON_ERROR));
        self::assertResponseStatusCodeSame(Response::HTTP_CREATED);

        $processId = $this->entityManager->getRepository(Process::class)->findOneBy([])->getId();

        $this->client->request(Request::METHOD_DELETE, '/api/process/' . $processId);
        self::assertResponseStatusCodeSame(Response::HTTP_NO_CONTENT);

        $this->client->request(Request::METHOD_DELETE, '/api/process/' . $processId);
        self::assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);
    }
}