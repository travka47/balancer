<?php

namespace App\Repository;

use App\Entity\Process;
use App\Entity\Workstation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
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
    private EntityManagerInterface $entityManager;

    public function __construct(ManagerRegistry $registry, EntityManagerInterface $entityManager)
    {
        parent::__construct($registry, Workstation::class);
        $this->entityManager = $entityManager;
    }

    public function findFreeWorkstation(Process $process): Workstation|null
    {
        $this->entityManager->clear();

        return $this->entityManager->createQuery(
            'SELECT w
            FROM App\Entity\Workstation w
            JOIN App\Entity\WorkstationResource wr WITH wr.workstation = w
            WHERE wr.freeRam >= :requiredRam
            AND wr.freeCpu >= :requiredCpu
            ORDER BY ((wr.freeRam - :requiredRam) / w.totalRam) + ((wr.freeCpu - :requiredCpu) / w.totalCpu) DESC'
        )->setMaxResults(1)
            ->setParameters([
                'requiredRam' => $process->getRequiredRam(),
                'requiredCpu' => $process->getRequiredCpu(),
            ])->getOneOrNullResult();
    }
}
