<?php

namespace App\DataFixtures;

use App\Domain\Entity\Admin;
use App\Domain\Enum\AdminRoleEnum;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Uid\Uuid;

class AdminFixtures extends Fixture
{
    private string $timezone;

    public function __construct(private ParameterBagInterface $params)
    {
        $this->timezone = $params->get('app.timezone');
    }

    public function load(ObjectManager $manager): void
    {

        $admin = new Admin();
        $admin->setEmail('admin@example.com')
            ->setUuid(Uuid::fromString('dd2f7b38-bf2d-47c0-8cb6-1894b348df12'))
            ->setFirstName('Admin')
            ->setLastName('Systemowy')
            ->setIsActive(true)
            ->setCreatedAt(new \DateTimeImmutable('now ', new \DateTimeZone($this->timezone)))
            ->setRoles([AdminRoleEnum::ADMIN->value])
            ->setPassword('$2y$13$RhqWYnUyvtQPj8AdBmNvrukVnmCFKLlOXMVlSg46ya36i1kRkbDaW');

        $manager->persist($admin);
        $manager->flush();
    }
}
