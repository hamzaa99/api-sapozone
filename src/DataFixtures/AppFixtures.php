<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $faker = Factory::create();

        for ($i = 0; $i < 50; $i++) {
            $user = new User();
            $user->setUsername($faker->username);
            $user->setemail($faker->email);
            $user->setname($faker->lastname);
            $user->setFirstname($faker->firstname);
            $user->setStreetname($faker->street);
            $user->setStreetNumber($faker->numberBetween(1, 99));
            $user->setPostalCode($faker->numberBetween(10000, 95000));
            $user->setcity($faker->city);
            $user->setPhoneNumber($faker->password);
            $user->setPassword($faker->password);

            $manager->persist($user);
        }

        $manager->flush();
    }
}
