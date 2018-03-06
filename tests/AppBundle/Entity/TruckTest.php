<?php
namespace tests\AppBundle\Entity;

use AppBundle\Entity\Truck;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Validator\Validation;

class TruckTest extends KernelTestCase
{
    private $truck;
    private $validator;

    public function setUp()
    {
        self::bootKernel();

        $this->truck->setTruckId("00886");
        $this->truck->setType("Large");

        // Gotta do some weird stuff because doing a validation on the class for unique
        $this->validator = Validation::createValidatorBuilder()->enableAnnotationMapping()->getValidator();
    }

    public function testFieldsCorrectSuccess()
    {
        // Validate the Truck
        $errors = $this->validator->validate($this->truck);
        // Assert that there are no errors
        $this->assertEquals(0, count($errors));
    }

    public function testIDIncorrectFailure()
    {
        // Test that seven characters in the ID will fail
        $this->truck->setTruckId("0088667");

        // Validate the Truck
        $errors = $this->validator->validate($this->truck);
        // Assert that there are no errors
        $this->assertEquals(1, count($errors));
        // Check the error message matches
        $this->assertEquals($errors[0] == "The Truck ID must be 6 numbers or fewer.");
    }

    public function testTypeIncorrectFailure()
    {
        // Test that seven characters in the ID will fail
        $this->truck->setType(str_repeat("A",16));

        // Validate the Truck
        $errors = $this->validator->validate($this->truck);
        // Assert that there are no errors
        $this->assertEquals(1, count($errors));
        // Check the error message matches
        $this->assertEquals($errors[0] === "The Truck Type must be 15 numbers or fewer.");
    }

}