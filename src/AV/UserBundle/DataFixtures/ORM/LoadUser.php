<?php
namespace AV\UserBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use AV\UserBundle\Entity\User;

class LoadUser implements FixtureInterface
{
    public function load(ObjectManager $manager)
    {
        // Les noms d'utilisateurs à créer
        $listNames = ['Aurélien', 'Marine', 'Anna'];

        foreach($listNames as $name)
        {
            // On crée l'utilisateur
            $user = new User;
            // Le nom d'utilisateur et le mot de passe sont identiques
            $user->setUsername($name);
            $user->setPassword($name);
            // On ne se sert pas du sel pour l'instant
            $user->setSalt('');
            // On définit uniquement le rôle ROLE_USER qui est le rôle de base
            $user->setRoles(['ROLE_USER']);
            // On le persiste
            $manager->persist($user);
        }
        // On déclenche l'enregistrement
        $manager->flush();
    }
}