<?php

namespace Tests\AppBundle\Repository;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use AppBundle\Entity\Contact;
use AppBundle\DataFixtures\ORM\LoadContactData;
use AppBundle\Entity\Address;
use AppBundle\Services\SearchNarrower;
use Tests\AppBundle\DatabasePrimer;

class ContactRepositoryTest extends KernelTestCase
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


   /* public static function setUpBeforeClass()
    {
        $em = static::$kernel->getContainer()
            ->get('doctrine')
            ->getManager();

        $contactLoader = new LoadContactData();
        $contactLoader->load($em);
    }
    */

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

        $contactLoader = new LoadContactData();
        $contactLoader->load($this->em);
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
        $contact->setRole("Property Manager");
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
        $repository->save($contact);

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
        $contact->setLastName("Jones");
        $contact->setEmailAddress("l@L.com");
        $contact->setRole("Property Manager");
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
        $id = $repository->save($contact);

        // query the database for the contact that was inserted
        $obtainedContact = $repository->getOne($id);

        // Retrieve the address out of the contact
        $obtainedAddress = $obtainedContact->getAddress();

        // Assert that the object retrieved is the same as the object that was inserted
        // Compare the original contact's properties to the properties of the Contact that was saved to the database
        $this->assertEquals($contact->getFirstName(), $obtainedContact->getFirstName());
        $this->assertEquals($contact->getLastName(), $obtainedContact->getLastName());
        $this->assertEquals($contact->getEmailAddress(), $obtainedContact->getEmailAddress());
        $this->assertEquals($contact->getRole(), $obtainedContact->getRole());
        $this->assertEquals($contact->getAddress(), $obtainedContact->getAddress());

        $this->assertEquals($address->getStreetAddress(), $obtainedAddress->getStreetAddress());
        $this->assertEquals($address->getPostalCode(), $obtainedAddress->getPostalCode());
        $this->assertEquals($address->getCity(), $obtainedAddress->getCity());
        $this->assertEquals($address->getProvince(), $obtainedAddress->getProvince());
        $this->assertEquals($address->getCountry(), $obtainedAddress->getCountry());


        //// Assert that the object retrieved is the same as the object that was inserted
        //// Loop through the original contact's properties and see if they match in the returned object.
        //// Can't just compare objects because the doctrine object contains extra garbage that the
        //// original one doesn't have.
        //foreach(get_object_vars($contact) as $property)
        //{
        //    $this->assertEquals($contact->$property, $obtainedContact->$property);
        //}
        //// Same for address
        //foreach(get_object_vars($address) as $property)
        //{
        //    $this->assertEquals($address->$property, $obtainedAddress->$property);
        //}
    }


    /**
     * Tests the insert functionality of the repository. Makes sure that data actaully gets inserted into the database properly
     */
    public function testSave()
    {
        // Create a new object
        $contact = new Contact();
        $contact->setFirstName("Bob");
        $contact->setLastName("Jones");
        $contact->setRole("Property Manager");
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
        $id = $repository->save($contact);
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
        $contact->setCompanyName("Doug's Dohnuts");
        $contact->setEmailAddress("l@L.com");
        $contact->setRole("Property Manager");

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
        $id = $repository->save($contact);

        //create replacement contact with same id
        $contact->setFirstName("Phillip");

        $contact->setAddress($address);

        //call update and pass in the id
        $repository->save($contact);
        /*
        $conn = $repository->getEntityManager()->getConnection();
        $sql = '
               SELECT * FROM Contact
                WHERE id = :id';
        $stmt = $conn->prepare($sql);
        $stmt->execute(['id' => $id]);
        $results = $stmt->fetchAll();

        //assertTrue($testAddress->getStreetAddress === "12345 test street");
        assertTrue(sizeof($results) == 1);*/


        $testContact = $repository->getOne($id);

        $this->assertTrue($testContact->getFirstName() === "Phillip");
    }


    /**
     * test that Contact objects are returned by the search
     */
    public function testContactObjectsReturned()
    {
        // get a repository to search with
        $repo = $this->em->getRepository(Contact::class);

        // create an array with values to search with
        $searches = array();
        $searches[] = 'Bob';
        $searches[] = 'Jones';

        // query the database
        $results = $repo->contactSearch($searches);

        // query the database
        //$results = $repo->contactSearch("Bob Jones");

        // create a new ReflectionClass object, using the returned object at index 0
        $resultReflection = new \ReflectionClass(get_class($results[0]));

        // Assert that the name of the Reflection object is 'Contact'
        $this->assertTrue($resultReflection->getShortName() == 'Contact');
    }

    /**
     * test that the SearchNarrower actually reduces rthe number of results from the initial query
     */
    public function testSearchNarrowerFunctionality()
    {
        // create a new SearchNarrower to be used later
        $searchNarrower = new SearchNarrower();

        // get a repository to search with
        $repo = $this->em->getRepository(Contact::class);

        // create an array with values to search with
        $cleanQuery = array();
        $cleanQuery[] = 'Bob';
        $cleanQuery[] = 'Jones';
        //try changing your php ini... didn't work with phpunit ocmmand line but it might for test explorer
        // Then you might be really screwed
        // Unless you can set the phpunit config for test explorer
        // query the database
        $results = $repo->contactSearch($cleanQuery);

        //$results = $repo->contactSearch("Bob Jones");

        //$cleanQuery = array();
        //$cleanQuery[] = 'Bob';
        //$cleanQuery[] = 'Jones';

        // narrow the searches so we only return exactlly what we want
        //var_dump($results);
        //var_dump("____________");
        $narrowedSearches = $searchNarrower->narrower($results, $cleanQuery, new Contact());
        //var_dump($narrowedSearches);

        // Assert that the size of the initial query is greater than the size of the narrowed query
        $this->assertTrue(sizeof($narrowedSearches) < sizeof($results));
    }

    /**
     * test that the search will work when an Address is specified
     */
    public function testSearchOnAddress()
    {
        // create a new SearchNarrower to be used later
        $repo = $this->em->getRepository(Contact::class);

        // create an array with values to search with
        $cleanQuery = array();
        $cleanQuery[] = 'Saskatoon';

        // query the database
        $results = $repo->contactSearch($cleanQuery);

        //$results = $repo->contactSearch("Saskatoon");

        // Assert that size of the query returns the expected number of results
        $this->assertEquals(20, sizeof($results));
    }

    //closes the memory mamnger
    /**
     * (@inheritDoc)
     */
    protected function tearDown()
    {
        parent::tearDown();

        $stmt = $this->em->getConnection()->prepare("DELETE FROM Contact");
        $stmt->execute();
        $stmt = $this->em->getConnection()->prepare("DELETE FROM Property");
        $stmt->execute();
        $stmt = $this->em->getConnection()->prepare("DELETE FROM Address");
        $stmt->execute();
        $stmt = $this->em->getConnection()->prepare("DELETE FROM Contact_Properties");
        $stmt->execute();

        $this->em->close();
        $this->em = null;//avoid memory meaks
    }
}