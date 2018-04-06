<?php
namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

use Symfony\Component\OptionsResolver\OptionsResolver;
use AppBundle\Entity\Property;
use AppBundle\Form\AddressType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\ResetType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;


class PropertyType extends AbstractType
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
        $builder
            ->add('siteId', IntegerType::class)
            ->add('propertyName', TextType::class, array('required'=>false))
            ->add('propertyType', ChoiceType::class, array('choices'=>Property::getTypes(), 'required'=>false, 'invalid_message' => 'Invalid property type'))
            ->add('propertyStatus', ChoiceType::class, array('choices'=>array_merge( array('...' => ""),Property::getStatuses()), 'invalid_message' => 'Invalid property status'))
            ->add('structureId', IntegerType::class, array('required'=>false))
            ->add('numUnits', IntegerType::class)
            ->add('neighbourhoodName', TextType::class)
            ->add('neighbourhoodId', TextType::class, array('required'=>false));

        // Add the address form into this form
        $builder->add('address', AddressType::class, array('label'=>false));



        //// Add a submit button
        //$builder->add('Submit', SubmitType::class);
    }


    /**
     * Configure the form to use the type of Property
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        // Configure the form to use the type of contact
        // Also make sure to cascade the validation to the address
        $resolver->setDefaults(array(
            'data_class' => Property::class
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'appbundle_property';
    }
}
