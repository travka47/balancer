<?php

namespace App\Repository;

use App\Entity\WorkstationResource;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<WorkstationResource>
 *
 * @method WorkstationResource|null find($id, $lockMode = null, $lockVersion = null)
 * @method WorkstationResource|null findOneBy(array $criteria, array $orderBy = null)
 * @method WorkstationResource[]    findAll()
 * @method WorkstationResource[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class WorkstationResourceRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, WorkstationResource::class);
    }
}
