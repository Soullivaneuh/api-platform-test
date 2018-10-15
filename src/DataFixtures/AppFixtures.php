<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\Article;
use App\Entity\Comment;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

final class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $article = (new Article())->setTitle('Best article I ever wrote.');
        $comment = (new Comment())
            ->setArticle($article)
            ->setContent('Calm down, it is not so good.')
        ;

        $manager->persist($article);
        $manager->persist($comment);
        $manager->flush();
    }
}