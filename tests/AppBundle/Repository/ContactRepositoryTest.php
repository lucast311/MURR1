<?php

namespace Tests\AppBundle\Repository;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use AppBundle\Entity\Contact;
use AppBundle\Entity\Address;

class ContactRepositoryTest extends KernelTestCase
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
     * Tests the functionality of the repository of getting all of the contacts out of the database
     */
    public function testGetAll()
    {
        // Create a contact and insert it to see if it comes back out
        // Create a new object
        $contact = new Contact();
        $contact->setFirstName("AAAAAAAAAAAAAAAAAAAAA");
        $contact->setLastName("Jons");
        $contact->setEmailAddress("l@L.com");

        // Have to create a new valid address too otherwise doctrine will fail
        $address = new Address();
        $address->setStreetAddress("12 15th st east");
        $address->setPostalCode("S0E1A0");
        $address->setCity("Saskatoon");
        $address->setProvince("Saskatchewan");
        $address->setCountry("Canada");
        $contact->setAddress($address);

        // Get the repository
        $repository = $this->em->getRepository(Contact::class);
        // Insert the contact
        $repository->insert($contact);

        // query the database
        $contacts = $repository->getAll();

        // Assert that it is an array of items
        $this->assertTrue(is_array($contacts));
        // assert that one of the objects in the array is in fact a contact object
        $this->assertTrue(is_a($contacts[0], Contact::class));
    }

    /**
     * Tests the functionality of the repository of getting a singular specified id out of the database
     */
    public function testGetOne()
    {
        // Create a contact and insert it to see if it comes back out
        // Create a new object
        $contact = new Contact();
        $contact->setFirstName("Bob");
        $contact->setLastName("Jons");
        $contact->setEmailAddress("l@L.com");

        // Have to create a new valid address too otherwise doctrine will fail
        $address = new Address();
        $address->setStreetAddress("12 15th st east");
        $address->setPostalCode("S0E1A0");
        $address->setCity("Saskatoon");
        $address->setProvince("Saskatchewan");
        $address->setCountry("Canada");
        $contact->setAddress($address);

        // Get the repository
        $repository = $this->em->getRepository(Contact::class);
        // Insert the contact and store the id
        $id = $repository->insert($contact);

        // query the database for the contact that was inserted
        $obtainedContact = $repository->getOne($id);

        // Retrieve the address out of the contact
        $obtainedAddress = $obtainedContact->getAddress();

        // Assert that the object retrieved is the same as the object that was inserted
        // Loop through the original contact's properties and see if they match in the returned object.
        // Can't just compare objects because the doctrine object contains extra garbage that the
        // original one doesn't have.
        foreach(get_object_vars($contact) as $property)
        {
            $this->assertEquals($contact->$property, $obtainedContact->$property);
        }
        // Same for address
        foreach(get_object_vars($address) as $property)
        {
            $this->assertEquals($address->$property, $obtainedAddress->$property);
        }
    }


    /**
     * Tests the insert functionality of the repository. Makes sure that data actaully gets inserted into the database properly
     */
    public function testInsert()
    {
        // Create a new object
        $contact = new Contact();
        $contact->setFirstName("Bob");
        $contact->setLastName("Jons");
        $contact->setEmailAddress("l@L.com");

        // Have to create a new valid address too otherwise doctrine will fail
        $address = new Address();
        $address->setStreetAddress("12 15th st east");
        $address->setPostalCode("S0E1A0");
        $address->setCity("Saskatoon");
        $address->setProvince("Saskatchewan");
        $address->setCountry("Canada");

        $contact->setAddress($address);
        //Get the repository for testing
        $repository = $this->em->getRepository(Contact::class);
        //Call insert on the repositor and record the id of the new object
        $id = $repository->insert($contact);
        //Assert that the id was returned
        $this->assertNotNull($id);
        //check the contact id is the same as the returned id
        $this->assertEquals($contact->getId(), $id);
    }

    //9c contact test
    public function testContactUpdate()
    {
        // Create a new object
        $contact = new Contact();
        $contact->setFirstName("Bob");
        $contact->setLastName("Jons");
        $contact->setEmailAddress("l@L.com");

        // Have to create a new valid address too otherwise doctrine will fail
        $address = new Address();
        $address->setStreetAddress("12 15th st east");
        $address->setPostalCode("S0E1A0");
        $address->setCity("Saskatoon");
        $address->setProvince("Saskatchewan");
        $address->setCountry("Canada");

        $contact->setAddress($address);
        //Get the repository for testing
        $repository = $this->em->getRepository(Contact::class);
        //Call insert on the repositor and record the id of the new object
        $id = $repository->insert($contact);

        //create replacement contact with same id
        $replaceContact = new Contact();
        $replaceContact->setFirstName("Phillip");
        $replaceContact->setLastName("Jons");
        $replaceContact->setEmailAddress("l@L.com");

        $replaceContact->setAddress($address); 

        //call update and pass in the id
        $repository->update($id, $replaceContact);

        $testContact = $repository->getOne($id); 

        assertTrue($testContact->getFirstName === "Philip"); 
    }


    //closes the memory mamnger
    /**
     * (@inheritDoc)
     */
    protected function tearDown()
    {
        parent::tearDown();

        $this->em->close();
        $this->em = null;//avoid memory meaks
    }

}