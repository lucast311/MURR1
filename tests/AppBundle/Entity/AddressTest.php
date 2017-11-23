<?php
namespace tests\ApBundle\Entity;

use AppBundle\Entity\Address;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Validation;



class AddressTest extends TestCase
{
    private $address;
    private $validator;

    protected function setUp()
    {
        // Create a new address and populate it with data
        $this->address = new Address();
        $this->address->setStreetAddress("123 Main Street");
        $this->address->setPostalCode("S7N 3K5");
        $this->address->setCity("Saskatoon");
        $this->address->setProvince("Saskatchewan");
        $this->address->setCountry("Canada");

        // Get a validator
        //$this->validator = $this->get('validator');
        $this->validator = Validation::createValidatorBuilder()->enableAnnotationMapping()->getValidator();
    }

    public function testAddressCorrect()
    {
        // Validate the address
        $errors = $this->validator->validate($this->address);
        // Assert that there are no errors
        $this->assertEquals(0, count($errors));
    }

 
    public function testAddressInvalidPostalCode()
    {
        // Make Address invalid
        $this->address->setPostalCode("6666");
        // Validate the address
        $errors = $this->validator->validate($this->address);
        // Assert that there is 1 error
        $this->assertEquals(1, count($errors));
    }

   
}