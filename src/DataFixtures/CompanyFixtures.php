<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Domain\Entity\Company;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Uid\Uuid;

class CompanyFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {

        $existingCompany = $manager->getRepository(Company::class)
            ->findOneBy(['shortName' => 'admin_cms']);

        if (!$existingCompany) {
            $company = new Company();
            $company->setUuid(Uuid::fromString('00000000-0000-4000-8000-000000000002'));
            $company->setShortName('admin_cms');
            $company->setLongName('Systemowa firma CMS');
            $company->setEmail('admin@cms.local');
            $company->setTaxNumber('0000000000');
            $company->setCountry('PL');
            $company->setCity('Adminowo');
            $company->setPostalCode('00-000');
            $company->setStreet('Systemowa');
            $company->setBuildingNumber('1');
            $company->setApartmentNumber(null);
            $company->setIsActive(true);
            $company->setIsDeleted(false);
            $company->setIsSystem(true);
            $company->setCreatedBy(Uuid::fromString('00000000-0000-4000-8000-000000000001'));

            $manager->persist($company);
            $manager->flush();

            echo "Systemowa firma admin_cms zostały utworzone.\n";
        } else {
            echo "Firma admin_cms już istnieje, pomijam.\n";
        }
    }
}
