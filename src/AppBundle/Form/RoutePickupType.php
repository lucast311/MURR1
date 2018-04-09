<?php
namespace AppBundle\Form;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use AppBundle\Entity\RoutePickup;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;

/**
 * RoutePickupType short summary.
 *
 * RoutePickupType description.
 *
 * @version 1.0
 * @author cst244
 */
class RoutePickupType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options){
        $builder
            ->add('container',null,array('invalid_message' => 'Please select a container to add', 'attr' => array('placeholder' => '...', 'class' => 'search')))
            ->add('pickupOrder', IntegerType::Class);
            //->add('Add', SubmitType::class);
    }

    /**
     * Configure the form to use the type of RoutePickup
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        // Configure the form to use the type of contact
        // Also make sure to cascade the validation to the address
        $resolver->setDefaults(array(
            'data_class' => RoutePickup::class
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'appbundle_routepickup';
    }
}