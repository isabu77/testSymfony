<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use App\Entity\Article;


class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $faker = \Faker\Factory::create();

        for ($i = 0 ; $i < 10 ; $i++){
            $article = new Article();
            $article->setTitle($faker->words(2, true))
            ->setContent($faker->paragraphs(2, true))
            ->setImage($faker->imageUrl(300, 250))
            ->setCreatedAt($faker->dateTimeBetween('-6 month'));
            
            $manager->persist($article);
        }

        $manager->flush();
    }
}
