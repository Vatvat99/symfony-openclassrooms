<?php

namespace AV\PlatformBundle\DoctrineListener;

use Doctrine\ORM\Event\LifecycleEventArgs;
use AV\PlatformBundle\Entity\Application;

class ApplicationNotification
{

    private $mailer;

    public function __construct(\Swift_Mailer $mailer)
    {
        $this->mailer = $mailer;
    }

    public function postPersist(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();

        // On ne veut envoyer un email que pour les entités Application
        if(!$entity instanceof Application)
        {
            return;
        }

        $message = new \Swift_Message(
            'Nouvelle candidature',
            'Vous avez reçu une nouvelle candidature.'
        );

        $message
            ->addTo('aurelien.vattant@gmail.com') //Idéalement, il faudrait ça $entity->getAdvert()->getAuthorEmail
            ->addFrom('admin@votresite.com')
        ;

        $this->mailer->send($message);
    }

}