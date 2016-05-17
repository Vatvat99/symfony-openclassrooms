<?php
namespace AV\PlatformBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use AV\PlatformBundle\Entity\Advert;

class LoadAdvert implements FixtureInterface
{
    // Dans l'argument de la méthode load, l'objet $manager est l'EntityManager
    public function load(ObjectManager $manager)
    {
        // Liste des annonces à créer
        $advert_list = [
            [
                'title' => 'Recherche développeur Symfony2',
                'author' => 'Alexandre',
                'content' => 'Nous recherchons un développeur Symfony2 débutant sur Lyon. Blabla...'
            ]
        ];

        foreach($advert_list as $advert)
        {
            // On crée l'annonce
            $new_advert = new Advert();
            $new_advert->setTitle($advert['title']);
            $new_advert->setAuthor($advert['author']);
            $new_advert->setContent($advert['content']);
            // On la persiste
            $manager->persist($new_advert);
        }

        // Et on déclenche l'enregistrement de toutes les catégories
        $manager->flush();
    }
}