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
        $this->assertEquals('Please specify a Site ID',$errors[0]->getMessage());
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
        $this->assertEquals('Please specify a valid Site ID',$errors[0]->getMessage());
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
     * Test the length of the propertyName at its boundary
     */
    public function testPropertyNameLengthValid()
    {
        // Make the property invalid
        $this->property->setPropertyName(str_repeat('a',100));

        // Validate the property
        $errors = $this->validator->validate($this->property);
        // Assert that there is 1 error
        $this->assertEquals(0, count($errors));
    }

    /**
     * Test the length of the propertyName past its boundary
     */
    public function testPropertyNameLengthInvalid()
    {
        // Make the property invalid
        $this->property->setPropertyName(str_repeat('a',101));

        // Validate the property
        $errors = $this->validator->validate($this->property);
        // Assert that there is 1 error
        $this->assertEquals(1, count($errors));
        $this->assertEquals('Property name must be less than 100 characters',$errors[0]->getMessage());
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
        $this->assertEquals('Invalid property type',$errors[0]->getMessage());
    }

    /**
     * Test the length of the property type at its boundary
     */
    public function testPropertyTypeLengthValid()
    {
        // Make the property invalid
        $this->property->setPropertyType(str_repeat('a',50));

        // Validate the property
        $errors = $this->validator->validate($this->property);
        // Assert that there is 1 error but it is of the choice not the length
        $this->assertEquals(1, count($errors));
        $this->assertEquals('Invalid property type',$errors[0]->getMessage());
    }

    /**
     * Test the length of the property type past its boundary
     */
    public function testPropertyTypeLengthInvalid()
    {
        // Make the property invalid
        $this->property->setPropertyType(str_repeat('a',51));

        // Validate the property
        $errors = $this->validator->validate($this->property);
        // Assert that there is 2 errors, 1 for the choice, 1 for the length
        $this->assertEquals(2, count($errors));
        //the length error comes first because it is first in the entity
        $this->assertEquals('Property type must be less than 50 characters',$errors[0]->getMessage());
        //The invalid type is the second error
        $this->assertEquals('Invalid property type',$errors[1]->getMessage());
    }

    /**
     * Make the property status blank and test that an error shows up
     */
    public function testPropertyStatusMissing()
    {
        // Make the property status blank
        $this->property->setPropertyStatus(null);
        // Validate the property
        $errors = $this->validator->validate($this->property);
        // Assert that there is 2 error
        //One will be for blank/null
        //One will be for invalid status
        $this->assertEquals(1, count($errors));
        $this->assertEquals('Please specify a Property Status',$errors[0]->getMessage());
        
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
        $this->assertEquals('Invalid property status',$errors[0]->getMessage());
    }

    /**
     * Test the length of the property status at its boundary
     */
    public function testPropertyStatusLengthValid()
    {
        // Make the property invalid
        $this->property->setPropertyStatus(str_repeat('a',50));

        // Validate the property
        $errors = $this->validator->validate($this->property);
        // Assert that there is 1 error but it is of the choice not the length
        $this->assertEquals(1, count($errors));
        $this->assertEquals('Invalid property status',$errors[0]->getMessage());
    }

    /**
     * Test the length of the property status past its boundary
     */
    public function testPropertyStatusLengthInvalid()
    {
        // Make the property invalid
        $this->property->setPropertyStatus(str_repeat('a',51));

        // Validate the property
        $errors = $this->validator->validate($this->property);
        // Assert that there is 2 errors, 1 for the choice, 1 for the length
        $this->assertEquals(2, count($errors));
        //the length error comes first because it is first in the entity
        $this->assertEquals('Property status must be less than 50 characters',$errors[0]->getMessage());
        //The invalid type is the second error
        $this->assertEquals('Invalid property status',$errors[1]->getMessage());
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
        $this->assertEquals('Please specify a neighbourhood name',$errors[0]->getMessage());
    }

    /**
     * Test the length of the neighbourhood name at its boundary
     */
    public function testPropertyNeighbourhoodNameLengthValid()
    {
        // Make the property invalid
        $this->property->setNeighbourhoodName(str_repeat('a',100));

        // Validate the property
        $errors = $this->validator->validate($this->property);
        // Assert that there is 1 error
        $this->assertEquals(0, count($errors));
    }

    /**
     * Test the length of the neighbourhood name past its boundary
     */
    public function testPropertyNeighbourhoodNameLengthInvalid()
    {
        // Make the property invalid
        $this->property->setNeighbourhoodName(str_repeat('a',101));

        // Validate the property
        $errors = $this->validator->validate($this->property);
        // Assert that there is 1 error
        $this->assertEquals(1, count($errors));
        $this->assertEquals('Neighbourhood Name must be less than 100 characters',$errors[0]->getMessage());
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
     * Test the length of the neighbourhood ID at its boundary
     */
    public function testNeighbourhoodIdLengthValid()
    {
        // Make the property invalid
        $this->property->setNeighbourhoodId(str_repeat('a',25));

        // Validate the property
        $errors = $this->validator->validate($this->property);
        // Assert that there is 1 error
        $this->assertEquals(0, count($errors));
    }

    /**
     * Test the length of the neighbourhood id past its boundary
     */
    public function testNeighbourhoodIdLengthInvalid()
    {
        // Make the property invalid
        $this->property->setNeighbourhoodId(str_repeat('a',26));

        // Validate the property
        $errors = $this->validator->validate($this->property);
        // Assert that there is 1 error
        $this->assertEquals(1, count($errors));
        $this->assertEquals('Neighbourhood ID must be less than 25 characters',$errors[0]->getMessage());
    }


    ///**
    // * Make the neighbourhood ID invalid and test that error shows up
    // */
    //public function testPropertyNeighbourhoodIdInvalid()
    //{
    //    // Make the property neighbourhood id invalid
    //    $this->property->setNeighbourhoodId(-1);
    //    // Validate the property
    //    $errors = $this->validator->validate($this->property);
    //    // Assert that there is 1 error
    //    $this->assertEquals(1, count($errors));
    //}

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
        $this->assertEquals('Please specify a valid number of units',$errors[0]->getMessage());
    }

    /**
     * Test that the number of units can not be blank
     */
    public function testPropertyNumUnitsBlank()
    {
        // Make the number of units invalid
        $this->property->setNumUnits(null);
        // Validate the property
        $errors = $this->validator->validate($this->property);
        // Assert that there is 1 error
        $this->assertEquals(1, count($errors));
        $this->assertEquals('Please specify the number of units',$errors[0]->getMessage());
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
        $this->assertEquals('Please specify a valid Structure ID',$errors[0]->getMessage());
    }
}