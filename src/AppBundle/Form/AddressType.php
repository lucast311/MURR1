<?php
namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use AppBundle\Entity\Address;
use Symfony\Component\Form\Extension\Core\Type\TextType;

/**
 * Type class for the Address entity. Builds a form for Address.
 */
class AddressType extends AbstractType
{
    /**
     * Used to build the form. The options for what is on the form
     * is in here.
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('streetAddress', TextType::class)
            ->add('postalCode', TextType::class)
            ->add('city', TextType::class)
            ->add('province', TextType::class)
            ->add('country', TextType::class);
    }

    /**
     * Configure the form to use the type of Address
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        // Configure the form to use the type of Address
        $resolver->setDefaults(array(
            'data_class' => Address::class
        ));
    }
}