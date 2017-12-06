<?php
namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use AppBundle\Entity\OOPs;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * This class is repsonsible for building a form for a Communication object
 */
class OOPsType extends AbstractType
{
    /**
     * This function will build the form to be displayed on a page
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        //generates an OOPs Form
        $builder
            ->add('binSerial', TextType::class,array(
                    'data' => ''
                    ))
            ->add('problemType', ChoiceType::class, array(
                    'choices' => OOPs::getProblemOptions()))
            ->add('description', TextType::class, array('required' => false))
            ->add('imageFile', FileType::class, array('required' => false))
            ->add('status', HiddenType::class, array(
                    'data' => 'Not yet started'))
            ->add('save', SubmitType::class, array('label' => 'Create OOPs Notice'));

    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => OOPs::class,
        ));
    }
}