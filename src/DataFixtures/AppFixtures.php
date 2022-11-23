<?php

namespace App\DataFixtures;

use App\Factory\PostFactory;
use App\Factory\UserFactory;
use App\Factory\CommentFactory;
use App\Factory\CategoryFactory;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        UserFactory::new()->create([
            'roles' => ['ROLE_ADMIN'],
            'email' => 'admin@gmail.com'
        ]);

        UserFactory::new()->createMany(10);
        UserFactory::createOne(['email' => 'user@gmail.com']);


        CategoryFactory::new()->createMany(5);

        // Instanciation de PostFactory en appelant la méthode statique new()
        PostFactory::new()

        // Création de 10 articles
        ->createMany(10);

        // Création des commentaires
        CommentFactory::new()->createMany(50);
        
        // $product = new Product();
        // $manager->persist($product);

        $manager->flush();
    }
}
