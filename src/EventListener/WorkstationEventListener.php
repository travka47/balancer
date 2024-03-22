<?php

namespace App\EventListener;

use App\Entity\Workstation;
use App\Entity\WorkstationResource;
use App\Repository\WorkstationResourceRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Event\PostPersistEventArgs;
use Doctrine\ORM\Event\PostRemoveEventArgs;

class WorkstationEventListener
{
    private EntityManagerInterface $entityManager;
    private WorkstationResourceRepository $workstationResourceRepository;

    public function __construct(
        EntityManagerInterface $entityManager,
        WorkstationResourceRepository $workstationResourceRepository,
    ) {
        $this->entityManager = $entityManager;
        $this->workstationResourceRepository = $workstationResourceRepository;
    }

    public function postPersist(PostPersistEventArgs $args): void
    {
        $entity = $args->getObject();

        if ($entity instanceof Workstation) {
            $resource = new WorkstationResource();
            $resource->setWorkstation($entity);

            $resource->setFreeRam($entity->getTotalRam());
            $resource->setFreeCpu($entity->getTotalCpu());

            $this->entityManager->persist($resource);
            $this->entityManager->flush();
        }
    }

    public function postRemove(PostRemoveEventArgs $args): void
    {
        $entity = $args->getObject();

        if ($entity instanceof Workstation) {
            $resource = $this->workstationResourceRepository->findOneBy(['workstation' => $entity]);

            if ($resource) {
                $this->entityManager->remove($resource);
                $this->entityManager->flush();
            }
        }
    }
}
