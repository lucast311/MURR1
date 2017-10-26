<?php

namespace tests\ApBundle\Entity;

use AppBundle\Entity\Contact;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Validation;

class ContactTest extends TestCase
{
    private $contact;
    private $validator;

    protected function setUp()
    {
        // Create a new contact and populate it with data
        $this->contact = new Contact();
        $this->contact->setFirstName("Ashton");
        $this->contact->setLastName("South");
        $this->contact->setprimaryPhone("3069215971");
        $this->contact->setEmailAddress("south@gmail.com");



        // Get a validator
        $this->validator = Validation::createValidator();
    }



    public function testContactCorrect()
    {
        //Validate the Contact
        $errors = $this->validator->validate($this->contact);
        // Assert that there are no errors
        $this->assertEquals(0, count($errors));
    }

    public function testContactFirstNameMissing()
    {
        // Make contact invalid
        $this->contact->setFirstName("");
        $errors = $this->validator->validate($this->contact);
        // Assert that there is 1 error
        $this->assertEquals(1, count($errors));

    }
    public function testContactLastNameMissing()
    {
        // Make contact invalid
        $this->contact->setLastName("");
        $errors = $this->validator->validate($this->contact);
        // Assert that there is 1 error
        $this->assertEquals(1, count($errors));
    }
    public function testContactPrimaryPhoneMissing()
    {
        // Make contact invalid
        $this->contact->setPrimaryPhpne("");
        $errors = $this->validator->validate($this->contact);
        // Assert that there is 1 error
        $this->assertEquals(1, count($errors));

    }

    public function testContactPrimaryPhoneInvalid()
    {
        // Make contact invalid
        $this->contact->setPrimaryPhpne("6");
        $errors = $this->validator->validate($this->contact);
        // Assert that there is 1 error
        $this->assertEquals(1, count($errors));

    }

    public function testContactEmailAddressMissing()
    {
        // Make contact invalid
        $this->contact->setEmailAddress("");
        $errors = $this->validator->validate($this->contact);
        // Assert that there is 1 error
        $this->assertEquals(1, count($errors));

    }

    public function testContactEmailAddressInvalid()
    {
        // Make contact invalid
        $this->contact->setEmailAddress("hello.com");
        $errors = $this->validator->validate($this->contact);
        // Assert that there is 1 error
        $this->assertEquals(1, count($errors));

    }

    public function testContactSecondaryPhoneInvalid()
    {
        // Make contact invalid
        $this->contact->setSecondaryPhpne("6");
        $errors = $this->validator->validate($this->contact);
        // Assert that there is 1 error
        $this->assertEquals(1, count($errors));
    }

    public function testContactPhoneExtentionInvalid()
    {
        // Make contact invalid
        $this->contact->setPhoneExtention("6");
        $errors = $this->validator->validate($this->contact);
        // Assert that there is 1 error
        $this->assertEquals(1, count($errors));

    }

    public function testContactFaxInvalid()
    {
        // Make contact invalid
        $this->contact->setFax("6");
        $errors = $this->validator->validate($this->contact);
        // Assert that there is 1 error
        $this->assertEquals(1, count($errors));

    }

    public function testContactMultipleErrors()
    {
        // Make contact invalid
        $this->contact->setPrimaryPhpne("6");
        $this->contact->setFax("6");
        $this->contact->setFirstName("");
        $errors = $this->validator->validate($this->contact);
        // Assert that there is 3 error
        $this->assertEquals(3, count($errors));

    }
}