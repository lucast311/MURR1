<?php

namespace Tests\AppBundle\Form\Type;

use AppBundle\Entity\Communication;
use Symfony\Component\Form\Test\TypeTestCase;
use AppBundle\Form\Type\CommunicationType;


class CommunicationTypeTest extends TypeTestCase
{

    public function testSubmitValidData()
    {
        //form test
        $formData = array(
          "date" => "2017-10-05",
          "type" => "Phone",
          "medium" => "incoming",
          "contact" => 1,
          "property" => 1,
          "category" => "Container",
          "description" => "Container has graffiti and needs to be cleaned. Action request made",
          "user" => 1
        );

        //creates a form
        $form = $this->factory->create(CommunicationType::class);

        //creates an object from the array
        $object = Communication::fromArray($formData);

        //submit the form
        $form->submit($formData);

        //makes sure the page doesnt have errors
        $this->assertTrue($form->isSynchronized());

        //make sure the form matches the objects data
        $this->assertEqauls($object, $form->getData());

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