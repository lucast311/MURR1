<?php
namespace Tests\AppBundle\Form;

use Symfony\Component\Form\Test\TypeTestCase;
use AppBundle\Form\ContactType;
use AppBundle\Entity\Contact;
use AppBundle\Entity\Address;

class ContactTypeTest extends TypeTestCase
{
    public function testSubmitData()
    {
        // The data that will be "submitted" to the form
        $formData = array(
            'firstName' => 'Jimmy',
            'lastName' => 'Jone',
            'companyName' => 'MURR',
            'primaryPhone' => '3066659999',
            'phoneExtension' => '9999',
            'secondaryPhone' => '5555555555',
            'emailAddress' => 'jimmy@jone.com',
            'fax' => '7894561232'
        );

        //create a new form
        $form = $this->factory->create(ContactType::class, new Contact());

        $object = new Contact();
        //populate the new address with the new data
        foreach ($formData as $key=>$value)
        {
            $methodName = "set" . $key;
            $object->$methodName($value);
        }

        //submit the data
        $form->submit($formData);

        // New address has to be created to match the new address doctrine would have created for us in the form.
        $object->setAddress(new Address());

        //Make sure the from doesent through exceptions
        $this->assertTrue($form->isSynchronized());
        //Check that the from contains the objects info.
       // $this->assertEquals($object,$form->getData());
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


