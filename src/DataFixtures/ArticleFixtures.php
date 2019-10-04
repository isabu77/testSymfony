<?php

namespace App\DataFixtures;

use App\Entity\Article;
use App\Entity\Comment;
use App\Entity\Category;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class ArticleFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $faker = \Faker\Factory::create();

        for ($c = 0 ; $c < 3 ; $c++){
            $category = new Category();
            $category->setTitle($faker->words(2, true))
            ->setDescription($faker->paragraphs(2, true));

            $manager->persist($category);

            for ($i = 0 ; $i < mt_rand(3,6) ; $i++){
                $article = new Article();
                $article->setTitle($faker->words(2, true))
                ->setContent($faker->paragraphs(5, true))
                ->setImage($faker->imageUrl(300, 250))
                ->setCreatedAt($faker->dateTimeBetween('-6 month'))
                ->setCategory($category);
                
                $manager->persist($article);

                for ($j = 0 ; $j < mt_rand(4,6) ; $j++){
                    $comment = new Comment();
                    $comment->setAuthor($faker->name)
                    ->setContent($faker->paragraphs(3, true))
                    ->setCreatedAt($faker->dateTimeBetween('-6 month'))
                    ->setArticle($article);
        
                    $manager->persist($comment);
                }
            }
        }
        $manager->flush();
    }
}
