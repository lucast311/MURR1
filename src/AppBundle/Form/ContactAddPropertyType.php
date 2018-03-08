<?php
namespace AppBundle\Form;

use AppBundle\Entity\Contact;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

/**
 * ContactAddPropertyType short summary.
 *
 * ContactAddPropertyType description.
 *
 * @version 1.0
 * @author cst244
 */
class ContactAddPropertyType extends AbstractType
{
    /**
     * Builds the form for associating a property to a contact
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('properties', null, array('label'=>'Property:'))
            ->add('contact', HiddenType::class, array('data'=>$options['contact']->getId()))
            ->add('Add', SubmitType::class);
    }

    /**
     * Sets the options for this class, use the class of Contact
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        // Configure the form to use the type of contact
        $resolver->setDefaults(array(
            'data_class' => Contact::class
        ));
    }

    /**
     * Story 4k
     * Sets the form name for this class
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'appbundle_propertyToContact';
    }
}
