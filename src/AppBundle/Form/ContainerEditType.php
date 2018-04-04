<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use AppBundle\Entity\Container;
use AppBundle\Entity\Property;

class ContainerEditType extends AbstractType
{


    /**
     * {@inheritdoc}
     * Builds an edit form for a container
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $em = $options['em'];
        $propRepo = $em->getRepository(Property::class);
        $properties = $propRepo->findAll();

        $blankProperty = array();
        $blankProperty[] = null;
        for ($i = 0; $i < sizeof($properties); $i++)
        {
        	$blankProperty[] = $properties[$i];
        }


        //Add all required fields for the edit form (No serial #, add property and structure)
        $builder
            ->add('containerSerial')
            ->add('type', ChoiceType::class, array('choices'=>Container::TypeChoices()))
            ->add('size')
            ->add('augmentation')
            ->add('status', ChoiceType::class, array('choices'=>Container::StatusChoices()))
            ->add('reasonForStatus')
            ->add('frequency', ChoiceType::class, array('choices'=>Container::FrequencyChoices()))
            ->add('locationDesc')
            ->add('lon')
            ->add('lat')
            ->add('property', EntityType::class, array('label'=>'Property:', 'choices' => $blankProperty, 'required' => false, 'class' => 'AppBundle:Property', 'attr' => array('class' => 'ui search dropdown')));
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Container',
            'em' => null,
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'appbundle_container';
    }
}
