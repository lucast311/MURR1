<?php

namespace Tests\AppBundle\Repository;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use AppBundle\Entity\Property;
use AppBundle\Entity\Address;

class PropertyRepositoryTest extends KernelTestCase
{
    /**
     * The entity manager
     * @var \Doctrine\ORM\EntityManager
     */
    private $em;

    /**
     * Just some setup stuff required by symfony for testing Repositories
     * {@inheritDoc}
     */
    protected function setUp()
    {
        self::bootKernel();

        $this->em = static::$kernel->getContainer()
            ->get('doctrine')
            ->getManager();
    }

    /**
     * Tests the insert functionality of the repository. Makes sure that data actaully gets inserted into the database properly
     */
    public function testInsert()
    {
        // Create a new object
        $property = new Property();
        $property->setSiteId(1593843);
        $property->setPropertyName("Charlton Arms");
        $property->setPropertyType("Townhouse Condo");
        $property->setPropertyStatus("Active");
        $property->setStructureId(54586);
        $property->setNumUnits(5);
        $property->setNeighbourhoodName("Sutherland");
        $property->setNeighbourhoodId("O48");

        // Have to create a new valid address too otherwise doctrine will fail
        $address = new Address();
        $address->setStreetAddress("12 15th st east");
        $address->setPostalCode("S0E1A0");
        $address->setCity("Saskatoon");
        $address->setProvince("Saskatchewan");
        $address->setCountry("Canada");

        $property->setAddress($address);

        //Get the repository for testing
        $repository = $this->em->getRepository(Property::class);
        //Call insert on the repository and record the id of the new object
        $id = $repository->insert($property);
        //Assert that the id was returned
        $this->assertNotNull($id);
        //check the contact id is the same as the returned id
        $this->assertEquals($property->getId(), $id);
    }

    /**
     * Tests the functionality of the repository of getting a singular specified id out of the database
     */
    public function testGetOne()
    {
        // Create a property and insert it to see if it comes back out
        // Create a new object
        $property = new Property();
        $property->setSiteId(1593843);
        $property->setPropertyName("Charlton Arms");
        $property->setPropertyType("Townhouse Condo");
        $property->setPropertyStatus("Active");
        $property->setStructureId(54586);
        $property->setNumUnits(5);
        $property->setNeighbourhoodName("Sutherland");
        $property->setNeighbourhoodId("O48");

        // Have to create a new valid address too otherwise doctrine will fail
        $address = new Address();
        $address->setStreetAddress("12 15th st east");
        $address->setPostalCode("S0E1A0");
        $address->setCity("Saskatoon");
        $address->setProvince("Saskatchewan");
        $address->setCountry("Canada");

        $property->setAddress($address);

        // Get the repository
        $repository = $this->em->getRepository(Property::class);
        // Insert the property and store the id
        $id = $repository->insert($property);

        // query the database for the contact that was inserted
        $obtainedProperty = $repository->getOne($id);

        // Retrieve the address out of the property
        $obtainedAddress = $obtainedProperty->getAddress();

        // Assert that the object retrieved is the same as the object that was inserted
        // Loop through the original property's properties and see if they match in the returned object.
        // Can't just compare objects because the doctrine object contains extra garbage that the
        // original one doesn't have.
        foreach(get_object_vars($property) as $attribute)
        {
            $this->assertEquals($property->$attribute, $obtainedProperty->$attribute);
        }
        // Same for address
        foreach(get_object_vars($address) as $attribute)
        {
            $this->assertEquals($address->$attribute, $obtainedAddress->$attribute);
        }
    }


    //closes the memory mamnger
    /**
     * (@inheritDoc)
     */
    protected function tearDown()
    {
        parent::tearDown();

        // Delete everything out of the property table after inserting stuff
        $stmt = $this->em->getConnection()->prepare('DELETE FROM Property');
        $stmt->execute();

        $this->em->close();
        $this->em = null;//avoid memory meaks
    }
}