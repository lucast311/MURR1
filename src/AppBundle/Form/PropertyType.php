<?php
namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

use Symfony\Component\OptionsResolver\OptionsResolver;

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
        $builder->add('siteId', IntegerType::class)
            ->add('propertyName', TextType::class, array('required'=>false))
            ->add('propertyType', ChoiceType::class, array('choices'=>Property::getTypes(), 'required'=>false))
            ->add('propertyStatus', ChoiceType::class, array('choices'=>array_merge( array('...' => ""),Property::getStatuses())))
            ->add('structureId', IntegerType::class, array('required'=>false))
            ->add('numUnits', IntegerType::class)
            ->add('neighbourhoodName', TextType::class)
            ->add('neighbourhoodId', TextType::class, array('required'=>false));

        // Add the address form into this form
        $builder->add('address', AddressType::class, array('label'=>false));

        // Add a clear button
        $builder->add('Clear', ResetType::class, array(
            'attr' => array('class' => 'save')
        ));

        // Add a submit button
        $builder->add('Submit', SubmitType::class);

    }


    public function configureOptions(OptionsResolver $resolver)
    {

    }
}