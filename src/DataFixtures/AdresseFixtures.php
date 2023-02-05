<?php

namespace App\DataFixtures;

use App\Factory\AdresseFactory;
use App\Factory\UserFactory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class AdresseFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        AdresseFactory::createMany(20);
    }

    public function getDependencies()
    {
        return [
            UserFixtures::class,
        ];
    }

}
