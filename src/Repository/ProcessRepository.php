<?php

namespace App\Repository;

use App\Entity\Process;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Process>
 *
 * @method Process|null find($id, $lockMode = null, $lockVersion = null)
 * @method Process|null findOneBy(array $criteria, array $orderBy = null)
 * @method Process[]    findAll()
 * @method Process[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProcessRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Process::class);
    }

    public function findAllSortedByRamAndCpuDesc(): array
    {
        return $this->findBy([], ['requiredRam' => 'DESC', 'requiredCpu' => 'DESC']);
    }

    public function findAllSortedByRamDesc(): array
    {
        return $this->findBy([], ['requiredRam' => 'DESC', 'requiredCpu' => 'ASC']);
    }

    public function findAllSortedByCpuDesc(): array
    {
        return $this->findBy([], ['requiredRam' => 'ASC', 'requiredCpu' => 'DESC']);
    }
}
