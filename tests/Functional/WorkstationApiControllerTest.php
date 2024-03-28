<?php

namespace App\Tests\Functional;

use App\DataFixtures\WorkstationFixture;
use App\Entity\Workstation;
use App\Tests\BalancerWebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class WorkstationApiControllerTest extends BalancerWebTestCase
{
    protected function createFixtures(): array
    {
        return [new WorkstationFixture()];
    }

    public function testIndex(): void
    {
        $this->client->request(Request::METHOD_GET, '/api/workstation');
        self::assertResponseStatusCodeSame(Response::HTTP_OK);

        $workstations = json_decode($this->client->getResponse()->getContent(), false, 512, JSON_THROW_ON_ERROR);

        self::assertObjectHasProperty('id', $workstations[0]);
        self::assertObjectHasProperty('totalRam', $workstations[0]);
        self::assertObjectHasProperty('totalCpu', $workstations[0]);
        self::assertObjectHasProperty('processes', $workstations[0]);
        self::assertObjectHasProperty('resource', $workstations[0]);

        self::assertEquals(100, $workstations[0]->totalRam);
        self::assertEquals(200, $workstations[0]->totalCpu);
        self::assertEquals([], $workstations[0]->processes);
        self::assertEquals(100, $workstations[0]->resource->freeRam);
        self::assertEquals(200, $workstations[0]->resource->freeCpu);
    }

    public function testCreate(): void
    {
        $this->client->request(Request::METHOD_POST, '/api/workstation', [], [], [], json_encode([
            'totalRam' => 100,
            'totalCpu' => 200,
        ], JSON_THROW_ON_ERROR));
        self::assertResponseStatusCodeSame(Response::HTTP_CREATED);

        $workstation = json_decode($this->client->getResponse()->getContent(), false, 512, JSON_THROW_ON_ERROR);

        self::assertObjectHasProperty('id', $workstation);
        self::assertObjectHasProperty('totalRam', $workstation);
        self::assertObjectHasProperty('totalCpu', $workstation);

        self::assertEquals(100, $workstation->totalRam);
        self::assertEquals(200, $workstation->totalCpu);
    }

    public function testDelete(): void
    {
        $workstationId = $this->entityManager->getRepository(Workstation::class)->findOneBy([])->getId();

        $this->client->request(Request::METHOD_DELETE, '/api/workstation/' . $workstationId);
        self::assertResponseStatusCodeSame(Response::HTTP_NO_CONTENT);

        $this->client->request(Request::METHOD_DELETE, '/api/workstation/' . $workstationId);
        self::assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);
    }
}
