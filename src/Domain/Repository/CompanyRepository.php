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

    public function getAllCompanies(): array
    {
        return $this->createQueryBuilder('c')
            ->where('c.isDeleted = false')
            ->getQuery()
            ->getResult();
    }

    public function getCompany(int $id): array
    {
        return $this->createQueryBuilder('c')
            ->where('c.isDeleted = false')
            ->andWhere('c.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getResult();
    }
}
