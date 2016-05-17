<?php

namespace AV\PlatformBundle\Validator;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class Antiflood extends Constraint
{
    public $message = "Vous avez déjà posté un message il y a moins de 15 secondes, merci d'attendre un peu.";

    /**
     * Spécifie que cette contrainte doit se faire valider non pas par AntifloodValidator mais par le service antiflood
     */
    public function validateBy()
    {
        return 'av_platform_antiflood'; // Ici, on fait appel à l'alias du service
    }

}