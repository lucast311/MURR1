<?php
namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use AppBundle\Entity\Communication;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Routing\Router;
/**
 * This class is repsonsible for building a form for a Communication object
 */
class CommunicationType extends AbstractType
{

        /**
         * @var Router
         */
        private $router;

        public function __construct(Router $router)
        {
            //Allows us to generate routes
            $this->router = $router;
        }


    /**
     * This function will build the form to be displayed on a page
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        //Note: Anywhere you see array_merge, I am added a default value. It is not included in the method call because the default value is not valid

        $builder
            //->add('date', DateType::class, array('label' => 'Date', 'invalid_message' => 'Please select a valid date')) //add date type field, and invalid message
            ->add('type', ChoiceType::class, array('label' => 'Type','choices' => array_merge( array('...' => '0'), Communication::getTypes()))) //add a type select box
            ->add('medium', ChoiceType::class, array('label' => 'Direction', 'expanded' => true, 'choices' => Communication::getMediums(), 'attr' => array("class"=>"fields"))) //add a medium radio button
            ->add('contactName', TextType::class, array('label' => 'Contact Name', 'required'=>false)) //add a contactName text box
            ->add('contactEmail', EmailType::class, array('label' => 'Contact Email', 'required'=>false)) //add a contactEmail text box
            ->add('contactPhone', TextType::class, array('label' => 'Contact Phone', 'required'=>false)) //add a contactPhone text box
            //search all properties in the database
            ->add('property',null,array('invalid_message' => 'Please select a valid property', 'attr' => array('placeholder' => '...','class' => 'search')))
            //->add('property', ChoiceType::class, array('label'=>'Property', 'choices' => array_merge( array('...' => 0),Communication::getProperties()))) //add a property select box
            ->add('category', ChoiceType::class, array('label' => 'Category', 'choices' => array_merge( array('...' => '0'),Communication::getCategories()))) //add a category select box
            ->add('description', TextareaType::class, array('label' => 'Description')); //add a description text area

        $builder->setAction($this->router->generate("new_communication"));
    }

    /**
     * Story 11d
     * Sets the form name for this class
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'appbundle_communication';
    }
}