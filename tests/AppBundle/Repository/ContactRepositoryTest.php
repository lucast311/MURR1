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