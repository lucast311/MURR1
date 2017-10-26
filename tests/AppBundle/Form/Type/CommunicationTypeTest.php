<?php

namespace Tests\AppBundle\Form\Type;

use AppBundle\Entity\Communication;
use Symfony\Component\Form\Test\TypeTestCase;
use AppBundle\Form\Type\CommunicationType;
use DateTime;


class CommunicationTypeTest extends TypeTestCase
{

    public function testSubmitValidData()
    {
        //form test
        $formData = array(
          "date" => null, //TODO: COME BACK TO ME
          "type" => "phone",
          "medium" => "incoming",
          "contact" => 1,
          "property" => 1,
          "category" => "container",
          "description" => "Container has graffiti and needs to be cleaned. Action request made"
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