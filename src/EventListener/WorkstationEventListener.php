<?php

namespace App\EventListener;

use App\Entity\Workstation;
use App\Entity\WorkstationResource;
use Doctrine\ORM\Event\PostPersistEventArgs;
use Doctrine\ORM\Event\PostRemoveEventArgs;

class WorkstationEventListener
{
    public function postPersist(PostPersistEventArgs $args): void
    {
        $entity = $args->getObject();

        if ($entity instanceof Workstation) {
            $entityManager = $args->getObjectManager();
            $workstationResource = new WorkstationResource();
            $workstationResource->setWorkstation($entity);
            $workstationResource->setFreeRam($entity->getTotalRam());
            $workstationResource->setFreeCpu($entity->getTotalCpu());
            $entityManager->persist($workstationResource);
            $entityManager->flush();
        }
    }

    public function postRemove(PostRemoveEventArgs $args): void
    {
        $entity = $args->getObject();

        if ($entity instanceof Workstation) {
            $entityManager = $args->getObjectManager();
            $workstationResource = $entityManager->getRepository(WorkstationResource::class)->findOneBy(['workstation' => $entity]);
            if ($workstationResource) {
                $entityManager->remove($workstationResource);
                $entityManager->flush();
            }
        }
    }
}
