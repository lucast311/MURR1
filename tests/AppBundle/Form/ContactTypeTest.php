<?php
namespace Tests\AppBundle\Form\Type;

use AppBundle\Form\Type\TestedType;
use AppBundle\Model\TestObject;
use Symfony\Component\Form\Test\TypeTestCase;
use AppBundle\Form\ContactType;
use AppBundle\Entity\Contact;
use AppBundle\Entity\Address;

class ContactTypeTest extends TypeTestCase
{
    public function testSubmitValidData()
    {
        // The data that will be "submitted" to the form
        $formData = array(
            'firstName' => 'Jimmy',
            'lastName' => 'Jone',
            'organization' => 'MURR',
            'officePhone' => '3066659999',
            'phoneExtention' => '9999',
            'mobilePhone' => '5555555555',
            'emailAddress' => 'jimmy@jone.com',
            'fax' => '7894561232',
            'streetAddress' => '123 Main Street',
            'postalCode' => 'S7N 4K6',
            'city' => 'Saskatoon',
            'province' => 'Saskatchewan',
            'country' => 'Canada'
        );

        // Create a new form and verify it compiles
        $form = $this->factory->create(ContactType::class);
       
       

        $form->submit($formData);

        $this->assertTrue($form->isSynchronized());
        //$this->assertEquals($object,$form->getData());

        $view = $form->createView();
        $children = $view->children;


        foreach (array_keys($formData) as $key)
        {
        	$this->assertArrayHasKey($key, $children);
        }

    }
}
