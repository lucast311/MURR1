<?php

namespace Tests\AppBundle\Form\Type;

use AppBundle\Entity\Communication;
use Symfony\Component\Form\Test\TypeTestCase;
use AppBundle\Form\Type\CommunicationType;
use DateTime;
use Symfony\Component\Validator\Validation;
use Symfony\Component\Form\Extension\Validator\ValidatorExtension;


class CommunicationTypeTest extends TypeTestCase
{
    /**
     * This method is required to allow the test to run
     * @return ValidatorExtension[]
     */
    protected function getExtensions()
    {
        return array(new ValidatorExtension(Validation::createValidator()));
    }

    public function testSubmitValidData()
    {
        //create a date to be applied to the data
        $date = new DateTime('now');
        $date->setTime(0,0,0);

        //form test
        $formData = array(
          "date" => $date,
          "type" => "Phone",
          "medium" => "Incoming",
          "contactName" => "John Smith",
          "contactEmail" => "email@email.com",
          "contactPhone" => "123-123-4567",
          "category" => "Container",
          "description" => "Container has graffiti and needs to be cleaned. Action request made",
          "property"=>null
        );

        //creates a form
        $form = $this->factory->create(CommunicationType::class);

        //creates an object from the array
        //$object = new Communication();
        //$object->setDate(new DateTime($formData['date']));
        //$object->setType($formData['type']);
        //$object->setMedium($formData['medium']);
        //$object->setContact($formData['contact']);
        //$object->setProperty($formData['property']);
        //$object->setCategory($formData['category']);
        //$object->setDescription($formData['description']);

        //submit the form
        $form->submit($formData);

        //makes sure the page doesnt have errors
        $this->assertTrue($form->isSynchronized());

        $formResponse = $form->getData();


        //make sure the form matches the objects data
        $this->assertEquals($formData, $formResponse);

        //get the form view and children
        $view = $form->createView();
        $children = $view->children;

        foreach(array_keys($formData) as $key)
        {
            //make sure the form has each key
            $this->assertArrayHasKey($key,$children);
        }
    }
}