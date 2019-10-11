<?php

namespace App\DataFixtures;

use App\Entity\User;
use App\Entity\Article;
use App\Entity\Comment;
use App\Entity\Category;
use App\Entity\ArticleLikes;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class ArticleFixtures extends Fixture
{
    private $encoder;
    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }


    public function load(ObjectManager $manager)
    {
        $faker = \Faker\Factory::create('fr_FR');

        $users = [];

        $user = new User();
        $user->setEmail("demo@demo.demo");
        $user->setUsername('Demo');
        $user->setPassword($this->encoder->encodePassword($user, "demo"));
        $manager->persist($user);
        $users[] = $user;

        for ($c = 0; $c < 30; $c++) {
            $user = new User();
            $user->setEmail($faker->email());
            $user->setUsername($faker->name());
            $user->setPassword($this->encoder->encodePassword($user, "demo"));
            $manager->persist($user);
            $users[] = $user;
        }

        for ($c = 0; $c < 3; $c++) {
            $category = new Category();
            $category->setTitle($faker->words(2, true))
                ->setDescription($faker->paragraphs(2, true));

            $manager->persist($category);

            for ($i = 0; $i < mt_rand(3, 6); $i++) {
                $article = new Article();
                $article->setTitle($faker->words(2, true))
                    ->setContent($faker->paragraphs(5, true))
                    ->setImage($faker->imageUrl(300, 250))
                    ->setCreatedAt($faker->dateTimeBetween('-6 month'))
                    ->setCategory($category)
                    ->setPrice($faker->randomFloat(2, 15, 300));

                $manager->persist($article);

                // les likes
                for ($l = 0; $l < mt_rand(0, 10); $l++) {
                    $like = new ArticleLikes();
                    $like->setArticle($article)
                    ->setUsers($faker->randomElement($users));
                    $manager->persist($like);

                }

                for ($j = 0; $j < mt_rand(4, 6); $j++) {
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
