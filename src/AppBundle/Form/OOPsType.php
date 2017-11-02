<?php

namespace AppBundle\Form;

use AppBundle\Entity\OOPs;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\OptionsResolver\OptionsResolver;


/**
 * OOPsType short summary.
 *
 * OOPsType description.
 *
 * @version 1.0
 * @author cst201
 */
class OOPsType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('image',FileType::class, array('label' => 'OOPs notice (image file)'));
    }


    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => OOPs::class,
        ));
    }
}