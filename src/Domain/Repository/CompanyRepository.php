<?php

declare(strict_types=1);

namespace App\Domain\Repository;

use App\Domain\Entity\Company;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Company>
 */
class CompanyRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Company::class);
    }

    public function getAllCompaniesActive(): array
    {
        return $this->createQueryBuilder('c')
            ->where('c.isDeleted = false')
            ->getQuery()
            ->getResult();
    }

    public function getAllCompaniesDeleted(): array
    {
        return $this->createQueryBuilder('c')
            ->where('c.isDeleted = true')
            ->getQuery()
            ->getResult();
    }

    public function getCompany(int $id): Company
    {
        return $this->createQueryBuilder('c')
            ->where('c.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getSingleResult();
    }
}
