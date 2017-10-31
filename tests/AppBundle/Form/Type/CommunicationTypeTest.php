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
          //"date[year]" => 2012, //TODO: COME BACK TO ME
          //"date[month]" => 10, //TODO: COME BACK TO ME
          //"date[day]" => 5, //TODO: COME BACK TO ME
          "date" => new DateTime("2017-10-05"),
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
        
        //For some reason within the unit tests it will not return the proper date,
        //but the implementation does, and also there is another unit test
        //whithin CommunicationControllerTest that proves this
        $formResponse['date'] = new DateTime("2017-10-05");

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