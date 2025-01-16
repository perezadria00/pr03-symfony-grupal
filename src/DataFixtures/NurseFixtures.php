<?php

namespace App\DataFixtures;

use App\Entity\Nurse;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class NurseFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $nurses = [
            ['user' => 'nurse_test', 'password' => 'password123', 'name' => 'Test', 'surname' => 'User'],
            ['user' => 'john_doe', 'password' => 'johnpass342', 'name' => 'John', 'surname' => 'Doe'],
        ];

        foreach ($nurses as $data) {
            $nurse = new Nurse();
            $nurse->setUser($data['user'])
                  ->setPassword($data['password']) 
                  ->setName($data['name'])
                  ->setSurname($data['surname']);

            $manager->persist($nurse);
        }

        $manager->flush();
    }
}
