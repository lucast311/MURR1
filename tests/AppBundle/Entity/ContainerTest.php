<?php
namespace tests\AppBundle\Entity;

use AppBundle\Entity\Container;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Validation;
use AppBundle\Entity\Property;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class ContainerTest extends KernelTestCase
{
    private $container;
    private $validator;

    protected function setUp()
    {
        self::bootKernel();

        // Create a new container and populate it with data
        $this->container = new Container();
        $this->container->setContainerSerial("XO6DEZM0");
        $this->container->setLocationDesc("The bin is out in the back of the building.");
        $this->container->setType("Bin");
        $this->container->setSize("6");
        $this->container->setStatus("Active");


        // Get a validator
        $this->validator = static::$kernel->getContainer()->get("validator");
    }

    /**
        Tests if a Container with correct fields passes validation.
    */
    public function testContainerAdded()
    {
        // Validate the container
        $error = $this->validator->validate($this->container);

        // Assert sure their are 0 errors
        $this->assertEquals(0, count($error));
    }

    /**
        Tests that a container doesn't pass validation if the serial code is too long
    */
    public function testContainerFailSerialTooLong()
    {
        // Change the Serial code to be 51 characters
        $this->container->setContainerSerial(str_repeat("a",51));

        // Attempt to validate the container
        $error = $this->validator->validate($this->container);

        // Assert that there is one error
        $this->assertEquals(1, count($error));
        // Assert that the error message is correct
        //$this->assertTrue("The container serial must be between 1 and 50 characters." === $error[0]->getMessage());
    }
    /**
        Tests that a container passes validation if the serial code is on the boundary
    */
    public function testContainerPassSerialOnBoundary()
    {
        // Change the Serial code to be 50 characters
        $this->container->setContainerSerial(str_repeat("a",50));

        // Attempt to validate the container
        $error = $this->validator->validate($this->container);

        // Assert that there are no errors
        $this->assertEquals(0, count($error));
    }

    /**
        Tests that a container doesn't pass validation if the Location Description is too long
    */
    public function testContainerFailLocationDescriptionTooLong()
    {
        // Change the Location Description to be 256 characters
        $this->container->setLocationDesc(str_repeat("a",256));

        // Attempt to validate the container
        $error = $this->validator->validate($this->container);

        // Assert that there is one error
        $this->assertEquals(1, count($error));
        // Assert that the error message is correct
        //$this->assertTrue("The location description must be below 250 characters." === $error[0]->getMessage());
    }
    public function testContainerPassLocationDescriptionOnBoundary()
    {
        // Change the Location Description to be 251 characters
        $this->container->setLocationDesc(str_repeat("a",250));

        // Attempt to validate the container
        $error = $this->validator->validate($this->container);

        // Assert that there are no errors
        $this->assertEquals(0, count($error));
    }

    // Test that a description can actually be blank
    public function testContainerPassLocationDescBlank()
    {
        $this->container->setLocationDesc("");

        // Attempt to validate the container
        $error = $this->validator->validate($this->container);

        // Assert that there are no errors
        $this->assertEquals(0, count($error));
    }

    //test that the type is invalid
    public function testContainerFailInvalidType()
    {
        // Change the Type to an invalid type
        $this->container->setType("Bonkatomic Punch!");

        // Attempt to validate the container
        $error = $this->validator->validate($this->container);

        // Assert that there is one error
        $this->assertEquals(1, count($error));
    }

    //test the length of Size is too long
    public function testContainerFailSizeTooLong()
    {
        // Change the Type to an invalid type
        $this->container->setSize(str_repeat("a",101));

        // Attempt to validate the container
        $error = $this->validator->validate($this->container);

        // Assert that there is one error
        $this->assertEquals(1, count($error));
    }

    //test the size is exactly 100 and passes
    public function testContainerPassSizeOnBoundary()
    {
        // Change the Type to an invalid type
        $this->container->setSize(str_repeat("a",100));

        // Attempt to validate the container
        $error = $this->validator->validate($this->container);

        // Assert that there are 0 errors
        $this->assertEquals(0, count($error));
    }

    //Test that the Serial number isn't blank
    public function testContainerSerialNotBlank()
    {
        $this->container->setContainerSerial("");

        $error = $this->validator->validate($this->container);

        $this->assertEquals(1, count($error));
    }

    //test that the serial is not null
    public function testContainerSerialNotNull()
    {
        $this->container->setContainerSerial(null);

        $error = $this->validator->validate($this->container);

        $this->assertEquals(2, count($error));
    }

    //test that the type is not null
    public function testContainerTypeNotNull()
    {
        $this->container->setType(null);

        $error = $this->validator->validate($this->container);

        $this->assertEquals(1, count($error));
    }

    //test the augmentation is exactly 50 characters
    public function testAugmentationIsCorrectSize()
    {
        $this->container->setAugmentation(str_repeat('a',50));

        $error = $this->validator->validate($this->container);

        $this->assertEquals(0, count($error));
    }

    //test that the augmentation is one less tha max (255)
    public function testAugmentationIsCorrectSizeBoundary()
    {
        $this->container->setAugmentation(str_repeat('a',254));

        $error = $this->validator->validate($this->container);

        $this->assertEquals(0, count($error));
    }

    //test that the augmentation is exactly the maximum (255)
    public function testAugmentationIsCorrectSizeExact()
    {
        $this->container->setAugmentation(str_repeat('a',255));

        $error = $this->validator->validate($this->container);

        $this->assertEquals(0, count($error));
    }

    //test augmentation is one over exact
    public function testAugmentationIsIncorrectSizeBoundary()
    {
        $this->container->setAugmentation(str_repeat('a',256));

        $error = $this->validator->validate($this->container);

        $this->assertEquals(1, count($error));
    }

    //test augmentation fails over the maximum
    public function testAugmentationIsIncorrectSize()
    {
        $this->container->setAugmentation(str_repeat('a',300));

        $error = $this->validator->validate($this->container);

        $this->assertEquals(1, count($error));
    }

    //test container fails for reason being too long
    public function testContainerFailReasonForStatusTooLong()
    {

        $this->container->setReasonForStatus(str_repeat("a",260));

        // Attempt to validate the container
        $error = $this->validator->validate($this->container);

        // Assert that there is one error
        $this->assertEquals(1, count($error));
    }

    //test Reason for status passes by beuing exactly maximum
    public function testContainerPassReasonForStatusOnBoundary()
    {

        $this->container->setReasonForStatus(str_repeat("a",255));

        // Attempt to validate the container
        $error = $this->validator->validate($this->container);

        // Assert that there are 0 errors
        $this->assertEquals(0, count($error));
    }

    //lat tests
    public function testLatitudeMaxBoundary()
    {
        $this->container->setLat(90);

        // Attempt to validate the container
        $error = $this->validator->validate($this->container);

        // Assert that there are 0 errors
        $this->assertEquals(0, count($error));
    }

    public function testLatitudeMaxBoundaryInvalid()
    {
        $this->container->setLat(100);

        // Attempt to validate the container
        $error = $this->validator->validate($this->container);

        // Assert that there are 0 errors
        $this->assertEquals(1, count($error));
    }

    public function testLatitudeMinBoundary()
    {
        $this->container->setLat(-90);

        // Attempt to validate the container
        $error = $this->validator->validate($this->container);

        // Assert that there are 0 errors
        $this->assertEquals(0, count($error));
    }

    public function testLatitudeMinBoundaryInvalid()
    {
        $this->container->setLat(-100);

        // Attempt to validate the container
        $error = $this->validator->validate($this->container);

        // Assert that there are 0 errors
        $this->assertEquals(1, count($error));
    }

    public function testLatitudeValid()
    {
        $this->container->setLat(0);

        // Attempt to validate the container
        $error = $this->validator->validate($this->container);

        // Assert that there are 0 errors
        $this->assertEquals(0, count($error));
    }

    //long tests

    public function testLongitudeMaxBoundary()
    {
        $this->container->setLong(180);

        // Attempt to validate the container
        $error = $this->validator->validate($this->container);

        // Assert that there are 0 errors
        $this->assertEquals(0, count($error));
    }

    public function testLongitudeMaxBoundaryInvalid()
    {
        $this->container->setLong(200);

        // Attempt to validate the container
        $error = $this->validator->validate($this->container);

        // Assert that there are 0 errors
        $this->assertEquals(1, count($error));
    }

    public function testLongitudeMinBoundary()
    {
        $this->container->setLong(-180);

        // Attempt to validate the container
        $error = $this->validator->validate($this->container);

        // Assert that there are 0 errors
        $this->assertEquals(0, count($error));
    }

    public function testLongitudeMinBoundaryInvalid()
    {
        $this->container->setLong(-200);

        // Attempt to validate the container
        $error = $this->validator->validate($this->container);

        // Assert that there are 0 errors
        $this->assertEquals(1, count($error));
    }

    public function testLongitudeValid()
    {
        $this->container->setLong(0);

        // Attempt to validate the container
        $error = $this->validator->validate($this->container);

        // Assert that there are 0 errors
        $this->assertEquals(0, count($error));
    }

    //status tests
    public function testValidStatusOptions()
    {
        //$validOptions = Container::StatusChoices();

        //all the valid test options
        $testOptions = array('Active', 'Inaccessable', 'Contaminated', 'Damage', 'Graffiti');

        //run through each option and check that they are valid
        foreach ($testOptions as $tester)
        {
            $this->container->setStatus($tester);
            $error = $this->validator->validate($this->container);
            $this->assertEquals(0, count($error));
        }

    }


    public function testInvalidStatusOptions()
    {

        $this->container->setStatus("Not a valid option");
        $error = $this->validator->validate($this->container);
        $this->assertEquals(1, count($error));


    }

    //frequency tests
     public function testValidFrequencyOptions()
    {
        //$validOptions = Container::StatusChoices();

        //all the valid test options
        $testOptions = array('Monthly', 'Weekly', 'Twice weekly');

        //run through each option and check that they are valid
        foreach ($testOptions as $tester)
        {
            $this->container->setFrequency($tester);
            $error = $this->validator->validate($this->container);
            $this->assertEquals(0, count($error));
        }

    }


    public function testInvalidFrequencyOptions()
    {
        $this->container->setFrequency("Not a valid option");
        $error = $this->validator->validate($this->container);
        $this->assertEquals(1, count($error));
    }


}

