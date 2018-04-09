<?php
namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use AppBundle\Entity\Route;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Doctrine\ORM\EntityRepository;


class RouteType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {                                                                                                                                                                                    //add appropriate classes
        $builder->add('routeId',  null, array('label' => 'Route ID:', 'attr' => array('maxlength' => 6)))
                ->add('template', EntityType::class, array('label' => 'Template:',
                                                           'class' => Route::class,
                                                           'query_builder' => function (EntityRepository $er) {
                                                               return $er->createQueryBuilder('rt')
                                                                   ->where('rt.template = 1');
                                                           },
                                                           'attr'  => array('placeholder' => '...', 'class' => 'search')));
    //          ->add('type',null, array('label'=>'Type:'))
    //          ->add('Add', SubmitType::class, array('attr' => array('class' => 'ui button')));
    }

    //null,array('label'=>'ID:','invalid_message' => 'The Truck ID [TRUCKID] is already in use.', /*'class' => 'AppBundle:Truck',*/ 'attr' => array('class' => ''/*ui search dropdown*/)))
    //                                                        //maybe use this at some point, UX stuff

    /**
     * Configure the form to use the Route type
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Route'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'appbundle_route';
    }
}
