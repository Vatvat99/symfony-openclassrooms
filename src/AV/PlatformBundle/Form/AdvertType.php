<?php

namespace AV\PlatformBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class AdvertType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('date',       'date')
            ->add('title',      'text')
            ->add('author',     'text')
            ->add('content',    'textarea')
            ->add('image',       new ImageType())
            ->add('categories', 'entity', [
                'class' => 'AVPlatformBundle:Category',
                'property' => 'name',
                'multiple' => true
            ])
            ->add('save',       'submit')
        ;

        // On ajoute une fonction qui va écouter l'événement PRE_SET_DATA
        // afin d'afficher un champ du formulaire qui soit différent selon si l'annonce est publiée ou non
        $builder->addEventListener(
            FormEvents::PRE_SET_DATA, // 1er argument : L'événement qui nous intéresse
            function(FormEvent $event) { // 2eme argument : La fonction à exécuter lorsque l'évènement est déclenché
                // On récupère notre objet Advert sous-jacent
                $advert = $event->getData();
                // Cette condition est importante, on en reparlera plus tard
                if($advert === null) {
                    return; // On sort de la fonction sans rien faire lorsque $advert vaut null
                }

                // Si l'annonce n'est pas publiée ou si elle n'existe pas encore en base
                if(!$advert->getPublished() || $advert->getId() === null) {
                    // Alors on ajoute le champ published
                    $event->getForm()->add('published', 'checkbox', ['required' => false]);
                } else {
                    // Sinon on le supprime
                    $event->getForm()->remove('published');
                }
            }
        );
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AV\PlatformBundle\Entity\Advert'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'av_platformbundle_advert';
    }
}
