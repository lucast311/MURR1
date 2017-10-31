<?php
namespace AppBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use AppBundle\Entity\Communication;

class CommunicationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        //Note: Anywhere you see array_merge, I am added a default value. It is not included in the method call because the default value is not valid

        $builder
            ->add('date', DateType::class, array('label' => 'Date'/*, 'invalid_message' => 'Please select a valid date'*/)) //add date type field, and invalid message
            ->add('type', ChoiceType::class, array('label' => 'Type','choices' => array_merge( array('...' => '0'), Communication::getTypes()))) //add a type select box
            ->add('medium', ChoiceType::class, array('expanded' => true, 'choices' => Communication::getMediums())) //add a medium radio button
            ->add('contact', ChoiceType::class, array('label' => 'Contact', 'choices' => array_merge( array('...' => 0),Communication::getContacts()))) //add a contact select box
            ->add('property', ChoiceType::class, array('label'=>'Property', 'choices' => array_merge( array('...' => 0),Communication::getProperties()))) //add a property select box
            ->add('category', ChoiceType::class, array('label' => 'Category', 'choices' => array_merge( array('...' => '0'),Communication::getCategories()))) //add a category select box
            ->add('description', TextareaType::class, array('label' => 'Description')) //add a description text area
            ->add('add', SubmitType::class, array('label' => 'Add')); //add a submit button

    }
}