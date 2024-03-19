<?php

namespace App\Repository;

use
    App\Entity\Workstation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Workstation>
 *
 * @method Workstation|null find($id, $lockMode = null, $lockVersion = null)
 * @method Workstation|null findOneBy(array $criteria, array $orderBy = null)
 * @method Workstation[]    findAll()
 * @method Workstation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class WorkstationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Workstation::class);
    }
}
