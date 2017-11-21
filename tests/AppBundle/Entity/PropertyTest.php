<?php

namespace tests\ApBundle\Entity;

use AppBundle\Entity\Property;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Validation;

class PropertyTest extends TestCase
{
    private $property;
    private $validator;

    public function setUp()
    {
        $this->property = new Property();
        $this->property->setId(1593843);
        $this->property->setPropertyName("Charlton Arms");
        $this->property->setPropertyType("Townhouse Condo");
        $this->property->setPropertyStatus("Active");
        $this->property->setStructureId(54586);
        $this->property->setNumUnits(5);
        $this->property->setNeighbourhoodName("Sutherland");
        $this->property->setNeighbourhoodId("O48");

        $this->validator = Validation::createValidatorBuilder()->enableAnnotationMapping()->getValidator();
    }

    /**
     * Tests that everything in the property validated to good
     */
    public function testPropertyCorrect()
    {
        //Validate the Property
        $errors = $this->validator->validate($this->property);
        // Assert that there are no errors
        $this->assertEquals(0, count($errors));
    }

    /**
     * make the site id bad and test to see that it made an error
     */
    public function testPropertySiteIdMissing()
    {
        // Make the property invalid
        $this->property->setId(null);
        // Validate the property
        $errors = $this->validator->validate($this->property);
        // Assert that there is 1 error
        $this->assertEquals(1, count($errors));
    }

    /**
     * make the site id invalid and test to see that it made an error
     */
    public function testPropertySiteIdInvalid()
    {
        // Make the property invalid
        $this->property->setId(-1);
        // Validate the property
        $errors = $this->validator->validate($this->property);
        // Assert that there is 1 error
        $this->assertEquals(1, count($errors));
    }

    /**
     * Make the property name blank and make sure it is still valid
     */
    public function testPropertyNameBlank()
    {
        // Make the property name invalid
        $this->property->setPropertyName('');
        // Validate the property
        $errors = $this->validator->validate($this->property);
        // Assert that there is 0 error
        $this->assertEquals(0, count($errors));
    }

    /**
     * Make the property type blank and test that no error shows up
     */
    public function testPropertyTypeBlank()
    {
        // Make the property type blank
        $this->property->setPropertyType(null);
        // Validate the property
        $errors = $this->validator->validate($this->property);
        // Assert that there is 0 error
        $this->assertEquals(0, count($errors));
    }

    /**
     * Make the property type invalid and test that an error shows up
     */
    public function testPropertyTypeInvalid()
    {
        // Make the property type invalid
        $this->property->setPropertyType('Cool house');
        // Validate the property
        $errors = $this->validator->validate($this->property);
        // Assert that there is 1 error
        $this->assertEquals(1, count($errors));
    }

    /**
     * Make the property status blank and test that an error shows up
     */
    public function testPropertyStatusMissing()
    {
        // Make the property status blank
        $this->property->setPropertyStatus('');
        // Validate the property
        $errors = $this->validator->validate($this->property);
        // Assert that there is 1 error
        $this->assertEquals(1, count($errors));
    }

    /**
     * Make the property status invalid and test that an error shows up
     */
    public function testPropertyStatusInvalid()
    {
        // Make the property name invalid
        $this->property->setPropertyStatus('Something invalid');
        // Validate the property
        $errors = $this->validator->validate($this->property);
        // Assert that there is 1 error
        $this->assertEquals(1, count($errors));
    }

    /**
     * Make the property status invalid and test that an error shows up
     */
    public function testPropertyNeighbourhoodNameMissing()
    {
        // Make the property neighbourhood name invalid
        $this->property->setNeighbourhoodName('');
        // Validate the property
        $errors = $this->validator->validate($this->property);
        // Assert that there is 1 error
        $this->assertEquals(1, count($errors));
    }

    /**
     * Make the neighbourhood ID empty and test that no error shows up
     */
    public function testPropertyNeighbourhoodIdBlank()
    {
        // Make the property neighbourhood id is blank
        $this->property->setNeighbourhoodId(null);
        // Validate the property
        $errors = $this->validator->validate($this->property);
        // Assert that there is 0 error
        $this->assertEquals(0, count($errors));
    }

    /**
     * Make the neighbourhood ID invalid and test that error shows up
     */
    public function testPropertyNeighbourhoodIdInvalid()
    {
        // Make the property neighbourhood id invalid
        $this->property->setNeighbourhoodId(-1);
        // Validate the property
        $errors = $this->validator->validate($this->property);
        // Assert that there is 1 error
        $this->assertEquals(1, count($errors));
    }

    /**
     * Make the property number of units invalid and test that an error shows up
     */
    public function testPropertyNumUnitsInvalid()
    {
        // Make the number of units invalid
        $this->property->setNumUnits(-1);
        // Validate the property
        $errors = $this->validator->validate($this->property);
        // Assert that there is 1 error
        $this->assertEquals(1, count($errors));
    }

    /**
     * Make the structure ID blank and check if there are no errors
     */
    public function testPropertyStructureIdBlank()
    {
        // Make the property structure id blank
        $this->property->setStructureId(null);
        // Validate the property
        $errors = $this->validator->validate($this->property);
        // Assert that there is 0 error
        $this->assertEquals(0, count($errors));
    }

    /**
     * Make the structure ID invalid and check if there are errors
     */
    public function testPropertyStructureIdInvalid()
    {
        // Make the property structure id blank
        $this->property->setStructureId(-1);
        // Validate the property
        $errors = $this->validator->validate($this->property);
        // Assert that there is 1 error
        $this->assertEquals(1, count($errors));
    }
}