<?php
namespace Tests\AppBundle\Form\Type;

use Symfony\Component\Form\Test\TypeTestCase;
use AppBundle\Form\ContactType;
use AppBundle\Entity\Contact;

class ContactTypeTest extends TypeTestCase
{
    public function testSubmitValidData()
    {
        // The data that will be "submitted" to the form
        $formData = array(
            'firstName' => 'Jimmy',
            'lastName' => 'Jone',
            'organization' => 'MURR',
            'primaryPhone' => '3066659999',
            'phoneExtention' => '9999',
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

        //Make sure the from doesent through exceptions
        $this->assertTrue($form->isSynchronized());
        //Check that the from contains the objects info.
        $this->assertEquals($object,$form->getData());
        //create the forms view
        $view = $form->createView();
        //get the children of the form
        $children = $view->children;
        //make sure the form has all the right fields
        foreach ($formData as $key)
        {
            $this->assertArrayHasKey($key,$children);
        }

    }
}
