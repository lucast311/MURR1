<?php
namespace Tests\AppBundle\Form\Type;

use AppBundle\Form\Type\TestedType;
use AppBundle\Model\TestObject;
use Symfony\Component\Form\Test\TypeTestCase;

class ContactTypeTest extends TypeTestCase
{
    public function testSubmitValidData()
    {
        // The data that will be "submitted" to the form
        $contactData = array(
            'firstName' => 'Jimmy',
            'lastName' => 'Jone',
            'organization' => 'MURR',
            'officePhone' => '3066659999',
            'phoneExtention' => '9999',
            'mobilePhone' => '5555555555',
            'emailAddress' => 'jimmy@jone.com',
            'fax' => '7894561232'
        );

        $addressData = array(
            'streetAddress' => '123 Main Street',
            'postalCode' => 'S7N 4K6',
            'city' => 'Saskatoon',
            'province' => 'Saskatchewan',
            'country' => 'Canada'
        );

        // Create a new form and verify it compiles
        $form = $this->factory->create(ContactType::class);
        //create a new contact object
        $contact = Contact::fromArray($contactData);
        // Create a new address object
        $address = Address::fromArray($addressData);


    }
}
