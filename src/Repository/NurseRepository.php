<?php

namespace App\Repository;

use App\Entity\Nurse;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Nurse>
 */
class NurseRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Nurse::class);
    }

    public function findByNameAndSurname(string $name, string $surname): array
    {
        return $this->createQueryBuilder('n')
            ->andWhere('n.name = :name')
            ->andWhere('n.surname = :surname')
            ->setParameter('name', $name)
            ->setParameter('surname', $surname)
            ->getQuery()
            ->getResult();
    }

   
 
}
