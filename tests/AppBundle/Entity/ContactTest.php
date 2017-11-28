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
        $this->contact->setCompanyName("COSMO!");
        $this->contact->setRole("Property Manager");
        $this->contact->setprimaryPhone("306-345-8932");
        $this->contact->setEmailAddress("south@gmail.com");

        // Get a validator
        $this->validator = Validation::createValidatorBuilder()->enableAnnotationMapping()->getValidator();
    }

    public function testContactCorrect()
    {
        //Validate the Contact
        $errors = $this->validator->validate($this->contact);
        // Assert that there are no errors
        $this->assertEquals(0, count($errors));
    }
    public function testContactFirstNameOnBoundary()
    {
        // Make First on boundary ( 150 characters )
        $this->contact->setFirstName("baaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa
                                      aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa
                                      aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa
                                      aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa");
        $errors = $this->validator->validate($this->contact);
        // Assert that there is 1 error
        $this->assertEquals(0, count($errors));
    }
    public function testContactFirstNameOverBoundary()
    {
        // Make First Name Invalid ( 151 characters )
        $this->contact->setFirstName("baaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa
                                      aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa
                                      aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa
                                      aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa");
        $errors = $this->validator->validate($this->contact);
        // Assert that there is 1 error
        $this->assertEquals(1, count($errors));
    }

    public function testContactFirstNamevValid()
    {
        // Make First Name valid
        $this->contact->setFirstName("Ashton");
        $errors = $this->validator->validate($this->contact);
        // Assert that there is 0 error
        $this->assertEquals(0, count($errors));
    }

    public function testContactCampanyNameOnBoundary()
    {
        // Make Company Name invalid ( 100 characters )
        $this->contact->setCompanyName("baaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa
                                        aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa
                                        aaaaaaaaaaaaaaaaaaaaaaa");
        $errors = $this->validator->validate($this->contact);
        // Assert that there is 0 error
        $this->assertEquals(0, count($errors));
    }
    public function testContactCampanyNameOverBoundary()
    {
        // Make Company Name invalid ( 101 characters )
        $this->contact->setCompanyName("baaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa
                                        aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa
                                        aaaaaaaaaaaaaaaaaaaaaaaa");
        $errors = $this->validator->validate($this->contact);
        // Assert that there is 1 error
        $this->assertEquals(1, count($errors));
    }

    public function testContactCampanyNameValid()
    {
        // Make Company Name valid
        $this->contact->setCompanyName("Siast");
        $errors = $this->validator->validate($this->contact);
        // Assert that there is 0 error
        $this->assertEquals(0, count($errors));
    }

    public function testContactPrimaryPhoneInvalid()
    {
        // Make contact invalid
        $this->contact->setPrimaryPhone("6");
        $errors = $this->validator->validate($this->contact);
        // Assert that there is 1 error
        $this->assertEquals(1, count($errors));
    }
    public function testContactPrimaryPhoneInvalidTooLong()
    {
        // Make contact invalid
        $this->contact->setPrimaryPhone("306-457-89072");
        $errors = $this->validator->validate($this->contact);
        // Assert that there is 1 error
        $this->assertEquals(1, count($errors));
    }
    public function testContactPrimaryPhoneValid()
    {
        // Make contact invalid
        $this->contact->setPrimaryPhone("306-457-8907");
        $errors = $this->validator->validate($this->contact);
        // Assert that there is 0 error
        $this->assertEquals(0, count($errors));
    }

    public function testContactSecondaryPhoneInvalid()
    {
        // Make contact invalid
        $this->contact->setSecondaryPhone("6");
        $errors = $this->validator->validate($this->contact);
        // Assert that there is 1 error
        $this->assertEquals(1, count($errors));
    }

    public function testContactSecondaryPhonevalid()
    {
        // Make contact valid
        $this->contact->setSecondaryPhone("306-457-8907");
        $errors = $this->validator->validate($this->contact);
        // Assert that there is 0 error
        $this->assertEquals(0, count($errors));
    }

    public function testContactPhoneExtentionInvalid()
    {
        // Make contact invalid
        $this->contact->setPhoneExtention(6);
        $errors = $this->validator->validate($this->contact);
        // Assert that there is 1 error
        $this->assertEquals(1, count($errors));
    }

    public function testContactPhoneExtentionValid()
    {
        // Make contact invalid
        $this->contact->setPhoneExtention(6444);
        $errors = $this->validator->validate($this->contact);
        // Assert that there is 0 error
        $this->assertEquals(0, count($errors));
    }

    public function testContactEmailAddressInvalid()
    {
        // Make contact invalid
        $this->contact->setEmailAddress("hello.com");
        $errors = $this->validator->validate($this->contact);
        // Assert that there is 1 error
        $this->assertEquals(1, count($errors));
    }
    public function testContactEmailAddressInvalidOnBoundary()
    {
        // Make contact invalid
        $this->contact->setEmailAddress("aaaaaaaaaaaaaaaaaaaaaaaaaaaaaa
                                         aaaaaaaaaaaaaaaaaaaaaaaaaaaaaa
                                         aaaaaaaaaaaaaaaaaaaaaaaaaaaaaa
                                         aaaaaa@aaaa.com");
        $errors = $this->validator->validate($this->contact);
        // Assert that there is 0 error
        $this->assertEquals(0, count($errors));
    }
    public function testContactEmailAddressInvalidOverBoundary()
    {
        // Make contact invalid
        $this->contact->setEmailAddress("baaaaaaaaaaaaaaaaaaaaaaaaaaaaaa
                                         aaaaaaaaaaaaaaaaaaaaaaaaaaaaaa
                                         aaaaaaaaaaaaaaaaaaaaaaaaaaaaaa
                                         aaaaaa@aaaa.com");
        $errors = $this->validator->validate($this->contact);
        // Assert that there is 1 error
        $this->assertEquals(1, count($errors));
    }
    public function testContactEmailAddressvalid()
    {
        // Make contact valid
        $this->contact->setEmailAddress("Siast@abc.com");
        $errors = $this->validator->validate($this->contact);
        // Assert that there is 1 error
        $this->assertEquals(0, count($errors));
    }

    public function testContactFaxInvalid()
    {
        // Make contact invalid
        $this->contact->setFax("6");
        $errors = $this->validator->validate($this->contact);
        // Assert that there is 1 error
        $this->assertEquals(1, count($errors));
    }
    public function testContactFaxInvalidTooLong()
    {
        // Make contact invalid
        $this->contact->setFax("306-457-89072");
        $errors = $this->validator->validate($this->contact);
        // Assert that there is 1 error
        $this->assertEquals(1, count($errors));
    }

    public function testContactFaxValid()
    {
        // Make contact valid
        $this->contact->setFax("306-457-8907");
        $errors = $this->validator->validate($this->contact);
        // Assert that there is 0 error
        $this->assertEquals(0, count($errors));
    }

    public function testContactMultipleErrors()
    {
        // Make contact invalid
        $this->contact->setPrimaryPhone("6");
        $this->contact->setFax("6");
        $errors = $this->validator->validate($this->contact);
        // Assert that there is 2 errors
        $this->assertEquals(2, count($errors));
    }

    public function testContacthasmorethanroll()
    {
        $this->contact->setFirstName("");
        $this->contact->setLastName("");
        $this->contact->setCompanyName("");
        $this->contact->setRole("Property Manager");
        $this->contact->setprimaryPhone("");
        $this->contact->setEmailAddress("");

        $errors = $this->validator->validate($this->contact);
        // Assert that there is 1 error
        $this->assertEquals(1, count($errors));
    }
    public function testContactblankform()
    {
        $this->contact->setFirstName("");
        $this->contact->setLastName("");
        $this->contact->setCompanyName("");
        $this->contact->setRole("");
        $this->contact->setprimaryPhone("");
        $this->contact->setEmailAddress("");

        $errors = $this->validator->validate($this->contact);
        // Assert that there is 1 error
        $this->assertEquals(1, count($errors));
    }

}