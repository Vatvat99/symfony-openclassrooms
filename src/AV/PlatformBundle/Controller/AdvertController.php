<?php

namespace AV\PlatformBundle\Controller;

// use AV\PlatformBundle\Entity\Advert;
// use AV\PlatformBundle\Entity\Image;
// use AV\PlatformBundle\Entity\Application;
use AV\PlatformBundle\Entity\Advert;
use AV\PlatformBundle\Form\AdvertType;
use AV\PlatformBundle\Form\AdvertEditType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
// use Symfony\Component\HttpFoundation\Response;
// use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class AdvertController extends Controller
{

    public function indexAction($page)
    {
        // On ne sait pas combien de pages il y a
        // Mais on sait qu'un n° de page doit être supérieur ou égal à 1
        if($page < 1)
        {
            // On déclenche une exception Not FoundException, cela va afficher
            // Une page d'erreur 404 (qu'on pourra personnaliser plus tard d'ailleurs)
            throw $this->createNotFoundException('La page "' . $page . '" n\'existe pas.');
        }

        // Ici je fixe le nombre d'annonces par page à 3
        // Mais bien sûr il faudrait utiliser un paramètre, et y accéder via $this->container->getParameter('nb_per_page')
        $nbPerPage = 3;

        // On récupère la liste des annonces
        $listAdverts = $this->getDoctrine()
            ->getManager()
            ->getRepository('AVPlatformBundle:Advert')
            ->getAdverts($page, $nbPerPage)
        ;

        // On calcule le nombre total de pages grâce au count($listAdverts) qui retourne le nombre total d'annonces
        $nbPages = ceil(count($listAdverts)/$nbPerPage);

        // Si la page n'existe pas, on retourne une 404
        if($page > $nbPages)
        {
            throw $this->createNotFoundException('La page ' . $page . 'n\'existe pas.');
        }

        // On appelle la vue en lui passant la liste
        return $this->render('AVPlatformBundle:Advert:index.html.twig', [
            'listAdverts' => $listAdverts,
            'nbPages'     => $nbPages,
            'page'        => $page
        ]);
    }

    public function viewAction($id)
    {
        // On récupère l'EntityManager
        $em = $this->getDoctrine()->getManager();
        // On récupère l'annonce correspondant à au paramètre $id
        $advert = $em->getRepository('AVPlatformBundle:Advert')->find($id);
        // $advert est donc une instance de AV\PlatformBundle\Entity\Advert
        // ou null si l'id $id n'existe pas
        // On vérifie que l'annonce existe bien
        if($advert === null)
        {
            throw $this->createNotFoundException('L\'annonce d\'id ' . $id . ' n\'existe pas.');
        }

        // On récupère la liste des AdvertSkill
        $listAdvertSkills = $em->getRepository('AVPlatformBundle:AdvertSkill')->findBy(['advert' => $advert]);

        return $this->render('AVPlatformBundle:Advert:view.html.twig', [
            'advert' => $advert,
            'listAdvertSkills' => $listAdvertSkills
        ]);
    }

    public function addAction(Request $request)
    {
        // On vérifie que l'utilisateur dispose bien du rôle ROLE_AUTEUR
        //if(!$this->get('security.context')->isGranted('ROLE_AUTEUR')) {
            // Si ce n'est pas le cas, on déclenche une exception "Accès interdit"
            //throw new AccessDeniedException('Accès limité aux auteurs.');
        //}

        // On crée un objet Advert
        $advert = new Advert();
        // On crée le formulaire
        $form = $this->createForm(new AdvertType, $advert);
        // On lie le formulaire à la requête et on vérifie que les valeurs entrées sont correctes
        if($form->handleRequest($request)->isValid())
        {
            // On enregistre notre objet $advert dans la bdd
            $em = $this->getDoctrine()->getManager();
            $em->persist($advert);
            $em->flush();
            // On affiche un message de confirmation
            $request->getSession()->getFlashBag()->add('notice', 'Annonce bien enregistrée.');
            // Puis on redirige vers la page de visualisation de l'annonce nouvellement créée
            return $this->redirect($this->generateUrl('av_platform_view', [
                'id' => $advert->getId(),
            ]));
        }
        // Si on arrive là, c'est que :
        // - Soit la requête est de type GET, donc le visiteur vient d'arriver sur la page et veut voir le formulaire
        // - Soit la requête est de type POST, mais le formulaire contient des erreurs invalide, donc on l'affiche
        return $this->render('AVPlatformBundle:Advert:add.html.twig', [
            'form' => $form->createView(),
        ]);

    }

    public function editAction($id, Request $request)
    {
        // On récupère l'EntityManager
        $em = $this->getDoctrine()->getManager();
        // On récupère l'annonce correspondant au paramètre $id
        $advert = $em->getRepository('AVPlatformBundle:Advert')->find($id);
        // Si l'annonce n'existe pas, on affiche une erreur 404
        if($advert === null)
        {
            throw $this->createNotFoundException('L\'annonce d\'id "' . $id . '" n\'existe pas.');
        }
        // On crée le formulaire à partir de l'annonce à modifier
        $form = $this->createForm(new AdvertEditType(), $advert);
        // On lie le formulaire à la requête et on vérifie que les valeurs entrées sont correctes
        if($form->handleRequest($request)->isValid()) {
            // Inutile de persister ici, Doctrine connaît dékà notre annonce
            $em->flush();
            // On affiche un message de confirmation
            $request->getSession()->getFlashBag()->add('notice', 'Annonce bien modifiée');
            // On redirige vers l'annonce modifiée
            return $this->redirect($this->generateUrl('av_platform_view', [
                'id' => $advert->getId()
            ]));
        }

        // Si on est là, c'est que le formulaire n'a pas été posté ou comporte des erreurs
        return $this->render('AVPlatformBundle:Advert:edit.html.twig', [
                'form'   => $form->createView(),
                'advert' => $advert // Je passe également l'annonce à la vue si jamais elle veut l'afficher
        ]);
    }

    public function deleteAction($id, Request $request)
    {
        // On récupère l'EntityManager
        $em = $this->getDoctrine()->getManager();
        // On récupère l'annonce correspondant à $id
        $advert = $em->getRepository('AVPlatformBundle:Advert')->find($id);
        // Si l'annonce n'existe pas, on affiche une erreur 404
        if($advert === null)
        {
            throw $this->createNotFoundException('L\'annonce d\'id "' . $id . '" n\'existe pas.');
        }
        // On crée un formulaire vide, qui ne contiendra que le champ CSRF
        // Cela permet de préotéger la suppression d'annonce contre cette faille
        $form = $this->createFormBuilder()->getForm();
        // On lie le formulaire à la requête et on vérifie qu'il est valide
        if($form->handleRequest($request)->isValid()) {
            // On supprime l'annonce
            $em->remove($advert);
            $em->flush();
            // On affiche un message de confirmation
            $request->getSession()->getFlashBag()->add('info', 'L\'annonce a bien été supprimée');
            // Puis on redirige vers la page d'accueil
            return $this->redirect($this->generateUrl('av_platform_home'));
        }

        // Si la requête est en GET, on affiche une page de confirmation avant de supprimer
        return $this->render('AVPlatformBundle:Advert:delete.html.twig', [
            'advert' => $advert,
            'form'   => $form->createView()
        ]);
    }

    public function menuAction($limit = 3)
    {
        $listAdverts = $this->getDoctrine()
            ->getManager()
            ->getRepository('AVPlatformBundle:Advert')
            ->findBy(
                [],                 // Pas de critères
                ['date' => 'desc'], // On trie par date décroissante
                $limit,             // On sélectionne $limit annonces
                0                   // A partir du premier
            )
        ;

        return $this->render('AVPlatformBundle:Advert:menu.html.twig', [
            'listAdverts' => $listAdverts
        ]);
    }

    public function translationAction($name)
    {
        return $this->render('AVPlatformBundle:Advert:translation.html.twig', [
           'name' => $name
        ]);
    }

}
