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
     * make the site id bad and test to see that it made an error
     */
    public function testPropertyNameMissing()
    {
        // Make the property invalid
        $this->property->setId(null);
        // Validate the property
        $errors = $this->validator->validate($this->property);
        // Assert that there is 1 error
        $this->assertEquals(1, count($errors));
    }
}