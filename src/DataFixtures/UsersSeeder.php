<?php


namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Persistence\ObjectManager;

class UsersSeeder
{
    private $om;

    public function __construct(ObjectManager $om) {
        $this->om = $om;
    }

    public function run() {
        $user = new User;
        $user->setEmail('legenza.przemyslaw@gmail.com')
            ->setFirstname('Przemysław')
            ->setLastname('Legenza')
            ->setEnabled(1)
            ->setPlainPassword($_ENV['API_PASSWORD'])
            ->setBirthdate(\DateTime::createFromFormat('Y-m-d', '1993-12-28'))
            ->setPhone('518469902')
            ->setUsername('api')
            ->setRoles(['ROLE_USER'])
            ->setSuperAdmin(false);
        $this->om->persist($user);
        $this->om->flush();
    }
}