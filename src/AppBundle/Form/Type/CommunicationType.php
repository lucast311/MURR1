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
        $builder
            ->add('date', DateType::class, array('label' => 'Date')) //add date type field
            ->add('type', ChoiceType::class, array('label' => 'Type','choices' => Communication::getTypes())) //add a type select box
            ->add('medium', ChoiceType::class, array('expanded' => true, 'choices' => Communication::getMediums())) //add a medium radio button
            ->add('contact', ChoiceType::class, array('label' => 'Contact', 'choices' => Communication::getContacts())) //add a contact select box
            ->add('property', ChoiceType::class, array('label'=>'Property', 'choices' => Communication::getProperties())) //add a property select box
            ->add('category', ChoiceType::class, array('label' => 'Category', 'choices' => Communication::getCategories())) //add a category select box
            ->add('description', TextareaType::class, array('label' => 'Description')) //add a description text area
            ->add('add', SubmitType::class, array('label' => 'Add')); //add a submit button
    }
}