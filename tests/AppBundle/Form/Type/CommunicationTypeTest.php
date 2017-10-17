<?php

namespace Tests\AppBundle\Form\Type;

use AppBundle\Form\Type\CommuniucationType;
use AppBundle\Entity\Communication;
use Symfony\Component\Form\Test\TypeTestCase;


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
          "description" => "Container has graffiti and needs to be cleaned. Action request made"
        );


        $form = $this->factory->create(CommuniucationType::class);

        $object = Communication::fromArray($formData);

        $form->submit($formData);
        $this->assertTrue($form->isSynchronized());
        $this->assertEqauls($object, $form->getData());

        $view = $form->createView();
        $children = $view->children;

        foreach(array_keys($formData) as $key)
        {
            $this->assertArrayHasKey($key,$children);
        }
    }
}