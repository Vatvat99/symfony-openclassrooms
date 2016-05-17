<?php
namespace AV\PlatformBundle\Beta;

use Symfony\Component\HttpKernel\Event\FilterResponseEvent;

class BetaListener
{
    // Notre processeur
    protected $betaHtml;

    // La date de fin de la version bêta :
    // - Avant cette date, on affichera un compte à rebours (J-3 par exemple)
    // - Après cette date, on n'affichera plus le "bêta"
    protected $endDate;

    public function __construct(BetaHtml $betaHtml, $endDate)
    {
        $this->betaHtml = $betaHtml;
        $this->endDate = new \Datetime($endDate);
    }

    public function processBeta(FilterResponseEvent $event)
    {
        // On teste si la requête est bien la requête principale (et non une sous-requête)
        if(!$event->isMasterRequest()) {
            return;
        }

        // On calcul le nombre de jour restant avant la fin de la bêta
        $remainingDays = $this->endDate->diff(new \Datetime())->format('%d');
        // Si la date de la bêta est dépassée
        if($remainingDays <= 0) {
            // on ne fait rien
            return;
        }

        // On récupère et modifie la réponse que le gestionnaire a inséré dans l'évènement
        $response = $this->betaHtml->displayBeta($event->getResponse(), $remainingDays);
        // Puis on renvoie la réponse modifiée dans l'évènement
        $event->setResponse($response);
    }
}