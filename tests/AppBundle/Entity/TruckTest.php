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

        $this->truck = (new Truck())
            ->setTruckId("00886")
            ->setType("Large");

        $this->validator = Validation::createValidatorBuilder()->enableAnnotationMapping()->getValidator();
    }


    /**
     * Story 40a
     * Tests that the default route weve spec'd validates
     */
    public function testSetupFieldsCorrect()
    {
        // Validate the Truck
        $errors = $this->validator->validate($this->truck);
        // Assert that there are no errors
        $this->assertEquals(0, count($errors));
    }

    //**TruckID VALIDATION TESTS**\\
    /**
     * Story 40a
     * Test that the ID won't validate if it contains letters
     */
    public function testIDLetterFailure()
    {
        // Test that 000Y8 will fail because of a letter
        $this->truck->setTruckId("000Y8");
        // Validate the Truck
        $errors = $this->validator->validate($this->truck);
        // Assert that there are errors
        $this->assertEquals(1, count($errors));
        // Convert array to string
        $errors = (string) $errors;
        // Check the error message matches
        $this->assertContains("The Truck ID must contain 1 to 6 digits, no letters", $errors);
    }

    /**
     * Story 40a
     * Test boundries for an incorrectID
     * -too long
     * -all 0's
     */
    public function testIDIncorrect()
    {
        // Test that seven characters in the ID will fail
        $this->truck->setTruckId("0088667");

        // Validate the Truck
        $errors = ($this->validator->validate($this->truck));
        // Assert that there are errors
        $this->assertEquals(1, count($errors));
        $errors = (string) $errors;
        // Check the error message matches
        $this->assertContains("Truck ID can not be more than 6 digits long", $errors);

        //// Test that 000000 will fail
        //$this->truck->setTruckId("000000");
        //// Validate the Truck
        //$errors = $this->validator->validate($this->truck);
        //// Assert that there are errors
        //$this->assertEquals(1, count($errors));
        //// Check the error message matches
        //$this->assertEquals($errors[0] == "Truck ID must be a valid number");//DIFFERENT MESSAGE //PAD THROUGH ORM?? //WOULD BE IN ENTITY GOOGLE "DOCTRINE 0 PAD ENTITY"
    }

    /**
     * Story 40a
     * Test that the ID will validate if valid
     */
    public function testIDCorrect()
    {
        // Test that 5 digits in the ID will work
        $this->truck->setTruckId("38888");
        // Validate the Truck
        $errors = $this->validator->validate($this->truck);
        // Assert that there are no errors
        $this->assertEquals(0, count($errors));
    }

    /**
     * Story 40a
     * Test that the ID will validate if valid on boundaries:
     * -max length
     * -min length
     */
    public function testIDCorrectBoundaries()
    {
        // Test that 6 characters in the ID will work
        $this->truck->setTruckId("100888");
        // Validate the Truck
        $errors = $this->validator->validate($this->truck);
        // Assert that there are no errors
        $this->assertEquals(0, count($errors));

        // Test that one character in the ID will work
        $this->truck->setTruckId("1");
        // Validate the Truck
        $errors = $this->validator->validate($this->truck);
        // Assert that there are no errors
        $this->assertEquals(0, count($errors));
    }


    //**Type VALIDATION TESTS**\\
    /**
     * Story 40a
     * Test that the Type won't validate if more than 15 characters or less than 1
     */
    public function testTypeIncorrect()
    {
        // Test that 16 characters in the Type will fail
        $this->truck->setType(str_repeat("A",16));

        // Validate the Truck
        $errors = $this->validator->validate($this->truck);
        // Assert that there are errors
        $this->assertEquals(1, count($errors));
        // Check the error message matches
        $this->assertContains("The Truck Type must contain 1-15 characters",(string) $errors);

        // Set the type to blank
        $this->truck->setType("");
        // Validate the Truck
        $errors = $this->validator->validate($this->truck);
        // Assert that there are errors
        $this->assertEquals(1, count($errors));
        // Check the error message matches
        $this->assertContains("Please specify a Type", (string) $errors);
    }

    /**
     * Story 40a
     * Test that the Type will validate if on boundaries
     * -15 characters
     * -1 character
     */
    public function testTypeCorrectBoundaries()
    {
        // Test that 15 characters in the Type will pass
        $this->truck->setType(str_repeat("A",15));
        // Validate the Truck
        $errors = $this->validator->validate($this->truck);
        //make sure no errors
        $this->assertEquals(0, count($errors));

        //------
        $this->truck->setType("A");
        // Validate the Truck
        $errors = $this->validator->validate($this->truck);
        //make sure no errors
        $this->assertEquals(0, count($errors));
    }

    /**
     * Story 40a
     * Test that the Type will validate if valid (10 characters)
     */
    public function testTypeCorrect()
    {
        // Test that seven characters in the ID will fail
        $this->truck->setType(str_repeat("A",10));
        // Validate the Truck
        $errors = $this->validator->validate($this->truck);
        //make sure no errors
        $this->assertEquals(0, count($errors));
    }
}