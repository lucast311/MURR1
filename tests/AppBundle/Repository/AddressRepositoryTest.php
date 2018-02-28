<?php

namespace Tests\AppBundle\Repository;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use AppBundle\Entity\Address;
use Tests\AppBundle\DatabasePrimer;

class AddressRepositoryTest extends KernelTestCase
{
    /**
     * The entity manager
     * @var \Doctrine\ORM\EntityManager
     */
    private $em;

    public static function setUpBeforeClass()
    {
        self::bootKernel();
        DatabasePrimer::prime(self::$kernel);
    }


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
     * Tests the insert functionality of the repository. Makes sure that data actaully gets inserted into the database properly.
     */
    public function testInsert()
    {
        // Create a new object
        $address = new Address();

        $address->setStreetAddress("12 15th st east");
        $address->setPostalCode("S0E1A0");
        $address->setCity("Saskatoon");
        $address->setProvince("Saskatchewan");
        $address->setCountry("Canada");

        //get the repo for testing
        $repository = $this->em->getRepository(Address::class);
        //insert address into database
        $id = $repository->save($address);
        //assert the id is not null
        $this->assertNotNull($id);
        //check the contact id is the same as the returned id
        $this->assertEquals($address->getId(), $id);
    }

	//9c address test
    public function testAddressUpdate()
    {
        // Create a new object
        $address = new Address();

        $address->setStreetAddress("12 15th st east");
        $address->setPostalCode("S0E1A0");
        $address->setCity("Saskatoon");
        $address->setProvince("Saskatchewan");
        $address->setCountry("Canada");

        //get the repo for testing
        $repository = $this->em->getRepository(Address::class);
        //insert address into database
        $id = $repository->save($address);
        //assert the id is not null

        $address->setStreetAddress("12345 test street");

        $repository->save($address);

        $testAddress = $repository->getOne($address);


        $this->assertTrue($testAddress->getStreetAddress() === "12345 test street");
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