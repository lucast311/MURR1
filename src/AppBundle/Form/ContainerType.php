<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use AppBundle\Entity\Container; 

class ContainerType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('Frequency', ChoiceType::class, array('choices'=>Container::FrequencyChoices()))
            ->add('containerSerial')
            ->add('locationDesc')
            ->add('long')
            ->add('lat')
            ->add('type', ChoiceType::class, array('choices'=>Container::TypeChoices()))
            ->add('size')
            ->add('augmentation')
            ->add('status', ChoiceType::class, array('choices'=>Container::StatusChoices()))
            ->add('reasonForStatus');
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Container'
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
