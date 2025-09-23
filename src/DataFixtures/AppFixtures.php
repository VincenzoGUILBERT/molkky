<?php

namespace App\DataFixtures;

use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    public function __construct(private UserPasswordHasherInterface $hasher) {}


    public function load(ObjectManager $manager): void
    {
        $faker = \Faker\Factory::create('fr_FR');

        $user = new \App\Entity\User();
        $user->setEmail('admin@gmail.com')
            ->setName('admin')
            ->setSurname('admin')
            ->setPassword($this->hasher->hashPassword($user, 'password'))
        ;
        
        $manager->persist($user);

        for ($i = 1; $i <= 10; $i++) {
            $event = new \App\Entity\Event();
            $event->setTitle($faker->sentence(3))
                ->setDescription($faker->paragraph())
                ->setDate(new \DateTimeImmutable("+$i days"))
                ->setLocation($faker->city())
                ->setNbrOfPlaces(10 + $faker->numberBetween(0, 20))
            ;
            
            $manager->persist($event);
        }

        $manager->flush();
    }
}
