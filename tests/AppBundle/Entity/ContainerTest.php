<?php
namespace tests\AppBundle\Entity;

use AppBundle\Entity\Container;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Validation;
use AppBundle\Entity\Property; 

class ContainerTest extends TestCase
{
    private $container;
    private $validator;

    protected function setUp()
    {
        // Create a new container and populate it with data
        $this->container = new Container();
        $this->container->setContainerSerial("XO6DEZM0");
        $this->container->setLocationDesc("The bin is out in the back of the building.");
        $this->container->setType("Recycle Bin");
        $this->container->setSize("6");
        $this->container->setStatus("Active");

        // Get a validator
        $this->validator = Validation::createValidatorBuilder()->enableAnnotationMapping()->getValidator();
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
        $this->assertTrue("The container serial must be between 1 and 50 characters." === $error[0]->getMessage());
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
        // Change the Location Description to be 251 characters
        $this->container->setLocationDesc(str_repeat("a",251));

        // Attempt to validate the container
        $error = $this->validator->validate($this->container);

        // Assert that there is one error
        $this->assertEquals(1, count($error));
        // Assert that the error message is correct
        $this->assertTrue("The location description must be below 250 characters." === $error[0]->getMessage());
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
        // Test that a description can actually be blank
    public function testContainerPassLocationReasonBlank()
    {
        $this->container->setReasonForInaccassability("");

        // Attempt to validate the container
        $error = $this->validator->validate($this->container);

        // Assert that there are no errors
        $this->assertEquals(0, count($error));
    }

    public function testContainerFailInvalidType()
    {
        // Change the Type to an invalid type
        $this->container->setType("Bonkatomic Punch!");

        // Attempt to validate the container
        $error = $this->validator->validate($this->container);

        // Assert that there is one error
        $this->assertEquals(1, count($error));
        // Assert that the error message is correct
        $this->assertTrue("You must select a valid container type!" === $error[0]->getMessage());
    }

    public function testContainerFailSizeTooLong()
    {
        // Change the Type to an invalid type
        $this->container->setSize(str_repeat("a",101));

        // Attempt to validate the container
        $error = $this->validator->validate($this->container);

        // Assert that there is one error
        $this->assertEquals(1, count($error));
        // Assert that the error message is correct
        $this->assertTrue("The size must be lower than 100 characters" === $error[0]->getMessage());
    }

    public function testContainerPassSizeOnBoundary()
    {
        // Change the Type to an invalid type
        $this->container->setSize(str_repeat("a",100));

        // Attempt to validate the container
        $error = $this->validator->validate($this->container);

        // Assert that there are 0 errors
        $this->assertEquals(0, count($error));
    }

    public function testContainerSerialNotBlank()
    {
        $this->container->setContainerSerial("");

        $error = $this->validator->validate($this->container);

        $this->assertEquals(1, count($error));
        // Assert that the error message is correct
        $this->assertTrue("The container serial must be between 1 and 50 characters." === $error[0]->getMessage());
    }

    public function testContainerSerialNotNull()
    {
        $this->container->setContainerSerial(null);

        $error = $this->validator->validate($this->container);

        $this->assertEquals(1, count($error));
        
        // Assert that the error message is correct
        $this->assertTrue("The container serial must be between 1 and 50 characters." === $error[0]->getMessage());
    }

    public function testContainerTypeNotNull()
    {
        $this->container->setType(null);

        $error = $this->validator->validate($this->container);

        $this->assertEquals(1, count($error));
        
        // Assert that the error message is correct
        $this->assertTrue("You must select a valid container type!" === $error[0]->getMessage());
    }

    public function testContainerPassProperty()
    {
    }


}

