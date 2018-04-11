<?php
namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use AppBundle\Entity\Route;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Doctrine\ORM\EntityRepository;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\EntityManager;

class RouteType extends AbstractType
{

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $em = $options['em'];
       // $emptyTemplate = (new Route())->setRouteId('.A.');
        //$em-
        $templateChoices = $em->getRepository(Route::class)->routeFilter(null,true);
        //array_push($templateChoices,(new Route())->setRouteId('...ed'));
        //add appropriate classes
        $builder->add('routeId',  null, array('label' => 'Route ID:', 'attr' => array('maxlength' => 6)))
                ->add('startDate', DateType::class, array(//'data_class'=> DateTime::createFromFormat(),
                                                          'required' => false,
                                                          'widget' => 'single_text',
                                                         // 'format' => 'M ',
                                                          'block_name'=>'startDate',
                                                          'label' => 'Start Date:',
                                                          'attr' => array('class'=>'datefield','placeholder'=>'Date')
                                                          ))

// $("#appbundle_route_startDate").parent().calendar({type:'date'})

//$("#appbundle_route_startDate").parent().calendar({type:'date',formatter:{date: function (date, settings) {
//var d = new Date(date),
//        month = '' + (d.getMonth() + 1),
//        day = '' + d.getDate(),
//        year = d.getFullYear();

//    if (month.length < 2) month = '0' + month;
//    if (day.length < 2) day = '0' + day;

//    return [year, month, day].join('-');
//    }}});
                ->add('template', EntityType::class, array('label' => 'Template:',
                                                           'class' => Route::class,
                                                           'choices' => $templateChoices,
                                                           'required' => false,
                                                           'placeholder' => (new Route())->setRouteId('None'),//'...',//'m.name', 'ASC'
                                                           'attr' => array( 'id'=>'templateDropdown','class' => 'search template' ),                                                           
                                                           'choice_attr' => array('class'=>'asdasd'),
                                                           'data' => null
                                                           ));
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
            'data_class' => 'AppBundle\Entity\Route',
            'em'=>null,
            'validation_groups' => array('Default', 'route')
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
