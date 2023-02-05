<?php

namespace App\DataFixtures;

use App\Factory\UserFactory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class UserFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        UserFactory::createOne(['username' => 'rubstr', 'email' => 'rubstr@example.com', 'roles' => ['ROLE_USER', 'ROLE_ADMIN']]);
        UserFactory::createOne(['username' => 'test', 'email' => 'test@example.com', 'roles' => ['ROLE_USER']]);
        UserFactory::createMany(10);

    }
}
