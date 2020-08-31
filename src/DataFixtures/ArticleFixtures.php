<?php

namespace App\DataFixtures;

use App\Entity\Article;
use App\Entity\Journalist;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Faker;

class ArticleFixtures extends Fixture
{

    public function load(ObjectManager $manager)
    {
        $faker = Faker\Factory::create('fr_FR');
        for ($i = 1; $i <= 5; $i++) {
            $article = new Article();
            $article->setTitle($faker->sentence);
            $article->setDescription($faker->paragraphs(3, true));
            $article->setPicture('article'.$i.'.jpg');
            $article->setPublicationDate($faker->dateTimeBetween($startDate = '-2 months', $endDate = 'now', $timezone = 'Europe/Paris'));

            $manager->persist($article);
        }

        $manager->flush();
    }
}
