<?php
namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use AppBundle\Entity\Contact;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\ResetType;

/**
 * Type class for the Contact entity. Builds a form for Contact.
 * Also includes the address form within it.
 */
class ContactType extends AbstractType
{
    /**
     * Used to build the form. The options for what is on the form
     * is in here.
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        // Add the fields
        $builder->add('firstName', TextType::class)
            ->add('lastName', TextType::class)
            ->add('organization', TextType::class, array('required'=>false))
            ->add('primaryPhone', TextType::class)
            ->add('secondaryPhone', TextType::class, array('required'=>false))
            ->add('phoneExtention', IntegerType::class, array('required'=>false))
            ->add('emailAddress', EmailType::class)
            ->add('fax', TextType::class, array('required'=>false));

        // Add the address form into this form
        $builder->add('address', AddressType::class, array('label'=>false));

        // Add a clear button
        $builder->add('reset', ResetType::class, array(
            'attr' => array('class' => 'save')
        ));

        // Add a submit button
        $builder->add('save', SubmitType::class);

    }

    /**
     * Configure the form to use the type of Contact
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        // Configure the form to use the type of contact
        // Also make sure to cascade the validation to the address
        $resolver->setDefaults(array(
            'data_class' => Contact::class
        ));
    }
}