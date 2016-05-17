<?php

namespace AV\PlatformBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class AdvertEditType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->remove('date');
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'av_platformbundle_advert_edit';
    }

    /**
     * Retourne une instance du formulaire parent
     * @return AdvertType
     */
    public function getParent()
    {
        return new AdvertType();
    }
}
