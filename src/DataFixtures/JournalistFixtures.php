<?php

namespace App\DataFixtures;

use App\Entity\Journalist;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Faker;

class JournalistFixtures extends Fixture
{

    private $encoder;

    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }

    public function load(ObjectManager $manager)
    {
        $faker = Faker\Factory::create('fr_FR');

        $testJournalist = new Journalist();
        $testJournalist->setRoles(['ROLE_USER']);
        $testJournalist->setFirstname('Test');
        $testJournalist->setLastname('Test');
        $testJournalist->setEmail('test@progres.fr');
        $testJournalist->setPassword($this->encoder->encodePassword(
            $testJournalist,
            'test123'
        ));

        $manager->persist($testJournalist);

        for ($i = 1; $i <= 5; $i++) {
            $journalist = new Journalist();
            $journalist->setRoles(['ROLE_USER']);
            $firstName = $faker->firstName;
            $lastname = $faker->lastName;
            $journalist->setFirstname($firstName);
            $journalist->setLastname($lastname);
            $journalist->setEmail(strtolower(substr($firstName, 0, 1).$lastname.'@progres.fr'));
            $journalist->setPassword($this->encoder->encodePassword(
                $journalist,
                'journaliste' . $i
            ));

            $manager->persist($journalist);
        }

        $manager->flush();
    }
}
