<?php

namespace Tests\AppBundle\Entity;

use PHPUnit\Framework\TestCase;
use AppBundle\Entity\Communication;
use AppBundle\Entity\Property;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class CommunicationTest extends KernelTestCase
{
    private $comm;
    private $validator;


    public function setUp()
    {
        self::bootKernel();

        //property object to store in the communication
        $property = new Property();

        $property->setSiteId(1593843);
        $property->setPropertyName("Charlton Arms");
        $property->setPropertyType("Townhouse Condo");
        $property->setPropertyStatus("Active");
        $property->setNumUnits(5);
        $property->setNeighbourhoodName("Sutherland");
        $property->setNeighbourhoodId("O48");

        $this->comm = new Communication();
        $this->comm->setType("Phone");
        $this->comm->setMedium("Incoming");
        $this->comm->setContactName("John Smith");
        $this->comm->setContactEmail("email@email.com");
        $this->comm->setContactPhone("306-123-4567");
        $this->comm->setProperty($property);
        $this->comm->setCategory("Container");
        $this->comm->setDescription("Bin will be moved to the eastern side of the building");

        //$this->validator = Validation::createValidatorBuilder()->enableAnnotationMapping()->getValidator();
        // Gotta do some weird stuff because doing a validation on the class for unique
        $this->validator = static::$kernel->getContainer()->get("validator");
    }

    /**
     * Story 11b
     *
     * Tests that the contact name is valid
     */
    public function testContactNameValid()
    {
        //Validate the communication
        $errors = $this->validator->validate($this->comm);
        // Assert that there are no errors
        $this->assertEquals(0, count($errors));
    }

    /**
     * Story 11b
     *
     * Tests that the contact name is too long and creates an error
     */
    public function testContactNameLengthBoundary()
    {
        //make the contact 256 characters
        $this->comm->setContactName(str_repeat('a',256));

        //Validate the communication
        $errors = $this->validator->validate($this->comm);
        // Assert that there are errors
        $this->assertEquals(1, count($errors));
        $this->assertEquals('Contact name must be less than 255 characters',$errors[0]->getMessage());
    }

    /**
     * Story 11b
     *
     * Tests that the contact name is not too long at the edge
     */
    public function testContactNameLengthEdge()
    {
        //make the contact 255 characters
        $this->comm->setContactName(str_repeat('a',255));

        //Validate the communication
        $errors = $this->validator->validate($this->comm);
        // Assert that there are no errors
        $this->assertEquals(0, count($errors));
    }

    /**
     * Story 11b
     *
     * Tests that the contact email is too long and creates an error
     */
    public function testContactEmailLengthBoundary()
    {
        //make the contact email 256 characters
        $this->comm->setContactEmail('a@a.' . str_repeat('a',252));

        //Validate the communication
        $errors = $this->validator->validate($this->comm);
        // Assert that there are errors
        $this->assertEquals(1, count($errors));
        $this->assertEquals('Contact email must be less than 255 characters',$errors[0]->getMessage());
    }

    /**
     * Story 11b
     *
     * Tests that the contact email is not too long at the edge
     */
    public function testContactEmailLengthEdge()
    {
        //make the contact email 255 characters
        $this->comm->setContactEmail('a@a.' . str_repeat('a',251));

        //Validate the communication
        $errors = $this->validator->validate($this->comm);
        // Assert that there are no errors
        $this->assertEquals(0, count($errors));
    }

    /**
     * Story 11b
     *
     * Tests that the contact phone invalid
     */
    public function testContactPhoneInvalid()
    {
        //make the contact phone invalid
        $this->comm->setContactPhone("123-123-123");

        //Validate the communication
        $errors = $this->validator->validate($this->comm);
        // Assert that there are errors
        $this->assertEquals(1, count($errors));
        $this->assertEquals('Phone number must be in the format of ###-###-####',$errors[0]->getMessage());
    }

    /**
     * Story 11b
     *
     * Tests that the contact email is not too long at the edge
     */
    public function testContactPhoneValid()
    {
        //make the contact email 255 characters
        $this->comm->setContactPhone("123-123-1234");

        //Validate the communication
        $errors = $this->validator->validate($this->comm);
        // Assert that there are no errors
        $this->assertEquals(0, count($errors));
    }

    /**
     * Story 11b
     *
     * Tests that the contact name can be blank
     */
    public function testNoContactName(){
        //make contact name blank
        $this->comm->setContactName("");

        //Validate the communication
        $errors = $this->validator->validate($this->comm);
        // Assert that there are no errors
        $this->assertEquals(0, count($errors));
    }

    /**
     * Story 11b
     *
     * Tests that the contact email can be blank
     */
    public function testNoContactEmail(){
        //make contact name blank
        $this->comm->setContactEmail("");

        //Validate the communication
        $errors = $this->validator->validate($this->comm);
        // Assert that there are no errors
        $this->assertEquals(0, count($errors));
    }

    /**
     * Story 11b
     *
     * Tests that the contact Phone can be blank
     */
    public function testNoContactPhone(){
        //make contact name blank
        $this->comm->setContactPhone("");

        //Validate the communication
        $errors = $this->validator->validate($this->comm);
        // Assert that there are no errors
        $this->assertEquals(0, count($errors));
    }

    //public function testGetSet()
    //{
    //    $com = new Communication();

    //    //date to be added to the object
    //    $date = new DateTime("2017-10-05");

    //    $com->setDate($date);
    //    $com->setType("phone");
    //    $com->setMedium("incoming");
    //    $com->setContact(1);
    //    $com->setProperty(1);
    //    $com->setCategory("container");
    //    $com->setDescription("Container has graffiti and needs to be cleaned. Action request made");
    //    $com->setUser(1);

    //    $this->assertEquals($com->getId(),0); //id is default 0
    //    $this->assertEquals($com->getDate(), $date);
    //    $this->assertEquals($com->getType(),"phone");
    //    $this->assertEquals($com->getMedium(),"incoming");
    //    $this->assertEquals($com->getContact(),1);
    //    $this->assertEquals($com->getProperty(),1);
    //    $this->assertEquals($com->getCategory(),"container");
    //    $this->assertEquals($com->getDescription(),"Container has graffiti and needs to be cleaned. Action request made");
    //    $this->assertEquals($com->getUser(),1);
    //}
}