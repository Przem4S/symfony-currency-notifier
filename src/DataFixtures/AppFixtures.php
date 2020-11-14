<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $users = new UsersSeeder($manager);
        $users->run();
        // $manager->persist($product);

        $manager->flush();
    }
}
