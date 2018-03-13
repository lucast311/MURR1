<?php
namespace AppBundle\Form;

use AppBundle\Entity\Contact;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

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
    private $contact;

    public function __construct(Contact $contact)
    {
        $this->contact = $contact;
    }

    /**
     * Builds the form for associating a property to a contact
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('property', EntityType::class, array('label'=>'Property:', 'class' => 'AppBundle:Property'))
            ->add('contact', HiddenType::class,array('data'=>$options['contact']))
            ->add('Add', SubmitType::class, array('attr' => array('class' => 'ui button', 'style' => 'display:inline-block')));
    }

    /**
     * Sets the options for this class, use the class of Contact
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        // Configure the form to use the type of contact
        //$resolver->setDefaults(array(
        //    'data_class' => Contact::class
        //));
        $resolver->setRequired('contact');
        $resolver->setAllowedTypes('contact',array('int'));
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
