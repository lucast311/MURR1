<?php
namespace Tests\AppBundle\Form;

use Symfony\Component\Form\Test\TypeTestCase;
use AppBundle\Entity\Address;
use AppBundle\Form\AddressType;

class AddressTypeTest extends TypeTestCase
{

    public function testAddressForm()
    {
        // The data that will be "submitted" to the form
        $formData = array(
            'streetAddress' => '123 Main Street',
            'postalCode' => 'S7N 4K6',
            'city' => 'Saskatoon',
            'province' => 'Saskatchewan',
            'country' => 'Canada'
        );

        //create a new form
        $form = $this->factory->create(AddressType::class, new Address());

        $object = new Address();
        //populate the new address with the new data
        foreach ($formData as $key=>$value)
        {
            $methodName = "set" . $key;
            $object->$methodName($value);
        }


        //submit the data
        $form->submit($formData);

        //Make sure the from doesent through exceptions
        $this->assertTrue($form->isSynchronized());
        //Check that the from contains the objects info.
        $this->assertEquals($object,$form->getData());
        //create the forms view
        $view = $form->createView();
        //get the children of the form
        $children = $view->children;
        //make sure the form has all the right fields
        foreach ($formData as $key=>$value)
        {
            $this->assertArrayHasKey($key,$children);
        }


    }
}