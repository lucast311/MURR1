<?php
namespace AppBundle\Form;

use AppBundle\Entity\Property;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
/**
 * PropertyAddContactType short summary.
 *
 * PropertyAddContactType description.
 *
 * @version 1.0
 * @author cst201
 */
class PropertyAddContactType extends AbstractType
{

    /**
     * Builds the form for associating a contact and a property
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('contact', EntityType::class, array('label'=>'Contact:', 'invalid_message' => 'The selected contact does not exist', 'class' => 'AppBundle:Contact', 'attr' => array('class' => 'ui search dropdown')))
            ->add('property', HiddenType::class,array('data'=>$options['property']))
            ->add('Add', SubmitType::class,  array('attr' => array('class' => 'ui button')));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setRequired('property'); 
        $resolver->setAllowedTypes('property', array('int')); 
    }

    public function getBlockPrefix()
    {
        return 'appbundle_contactToProperty'; 
    }
}