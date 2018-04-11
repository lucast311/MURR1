<?php
namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use AppBundle\Entity\Route;


class RouteTemplateType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {                                                                                                                                                                                    //add appropriate classes
        $builder->add('routeId', null, array('label'=>'Template Name:', 'attr' => array('maxlength' => 20)));
    }

    //null,array('label'=>'ID:','invalid_message' => 'The Truck ID [TRUCKID] is already in use.', /*'class' => 'AppBundle:Truck',*/ 'attr' => array('class' => ''/*ui search dropdown*/)))
    //                                                        //maybe use this at some point, UX stuff

    /**
     * Configure the form to use the Route type
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Route',
            'validation_groups' => array('Default', 'template')
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'appbundle_route_template';
    }
}
