<?php
namespace Tests\AppBundle\Repository;

use AppBundle\Entity\Communication;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use DateTime;
use AppBundle\Entity\Property;
use AppBundle\Entity\Address;
use AppBundle\Services\Cleaner;
use AppBundle\Services\SearchNarrower;
use AppBundle\DataFixtures\ORM\LoadCommunicationData;
use Tests\AppBundle\DatabasePrimer;

class CommunicationRepositoryTest extends KernelTestCase
{
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $em;

    public static function setUpBeforeClass()
    {
        self::bootKernel();
        DatabasePrimer::prime(self::$kernel);
    }


    /**
     * {@inheritDoc}
     */
    protected function setUp()
    {
        self::bootKernel();

        $this->em = static::$kernel->getContainer()
            ->get('doctrine')
            ->getManager();

        $communicationLoader = new LoadCommunicationData();
        $communicationLoader->load($this->em);
    }


    public function testInsert()
    {
        //create a property for the communication
        $property = new Property();
        $property->setSiteId(1593843);
        $property->setPropertyName("Charlton Arms");
        $property->setPropertyType("Townhouse Condo");
        $property->setPropertyStatus("Active");
        $property->setNumUnits(5);
        $property->setNeighbourhoodName("Sutherland");
        $property->setNeighbourhoodId("O48");

        //create an address for the property
        $address = new Address();
        $address->setStreetAddress("12 15th st east");
        $address->setPostalCode("S0E 1A0");
        $address->setCity("Saskatoon");
        $address->setProvince("Saskatchewan");
        $address->setCountry("Canada");
        $property->setAddress($address);

        //create a communication
        $comm = new Communication();
        $comm->setType("In Person");
        $comm->setMedium("Incoming");
        $comm->setContactName("John Smith");
        $comm->setContactEmail("email@email.com");
        $comm->setContactPhone("306-123-4567");
        $comm->setProperty($property);
        $comm->setCategory("Container");
        $comm->setDescription("Bin will be moved to the eastern side of the building");

        //add to database and return the ID
        $id = $this->em->getRepository(Communication::class)
            ->insert($comm);

        $this->assertEquals($id,$comm->getId());
    }

    /**
     * Story 11b
     * Tests that the current date is being stored in the database
     */
    public function testCurrentDateStored(){
        //create a property for the communication
        $property = new Property();
        $property->setSiteId(1593844);
        $property->setPropertyName("Charlton Arms");
        $property->setPropertyType("Townhouse Condo");
        $property->setPropertyStatus("Active");
        $property->setNumUnits(5);
        $property->setNeighbourhoodName("Sutherland");
        $property->setNeighbourhoodId("O48");

        //create an address for the property
        $address = new Address();
        $address->setStreetAddress("12 15th st east");
        $address->setPostalCode("S0E 1A0");
        $address->setCity("Saskatoon");
        $address->setProvince("Saskatchewan");
        $address->setCountry("Canada");
        $property->setAddress($address);

        //create a communication
        $comm = new Communication();
        $comm->setType("In Person");
        $comm->setMedium("Incoming");
        $comm->setContactName("John Smith");
        $comm->setContactEmail("email@email.com");
        $comm->setContactPhone("306-123-4567");
        $comm->setProperty($property);
        $comm->setCategory("Container");
        $comm->setDescription("Bin will be moved to the eastern side of the building");


        $repo = $this->em->getRepository(Communication::class);
        $id = $repo->insert($comm);

        $dbComm = $repo->findOneById($id);

        $date = new DateTime('now');
        $dateString = $date->format('Y-m-d');

        $this->assertEquals($dbComm->getDate(), $dateString);
    }

    /**
     * Story 11c
     * Test that a communication object is returned
     */
    public function testCommunicationObjectsReturned()
    {
        // get a repository to search with
        $repo = $this->em->getRepository(Communication::class);

        // create an array with values to search with
        $searches = array();
        $searches[] = 'Collection';

        // query the database
        $results = $repo->communicationSearch($searches);

        // create a new ReflectionClass object, using the returned object at index 0
        //$resultReflection = new \ReflectionClass(get_class($results[0]));

        // Assert that the name of the Reflection object is 'Communication'
        //$this->assertTrue($resultReflection->getShortName() == 'Communication');

        $this->AssertTrue(is_a($results[0],Communication::class));
    }

    /**
     * Story 11c
     * test that a communication can be searched for using only one specified string
     */
    public function testSearchByOnlyOneField()
    {
        // create a new Repository to be used later
        $repo = $this->em->getRepository(Communication::class);

        // create an array with values to search with
        $cleanQuery = array();
        $cleanQuery[] = '2018-01-01';

        // query the database
        $results = $repo->CommunicationSearch($cleanQuery);

        // Assert that size of the query returns the expected number of results
        $this->assertEquals(3, sizeof($results));
    }

    /**
     * Story 11c
     * test that the search narrower functions
     */
    public function testSearchNarrowerFunctionality()
    {
        // create a new Repository to be used later
        $searchNarrower = new SearchNarrower();

        // get a repository to search with
        $repo = $this->em->getRepository(Communication::class);

        // create an array with values to search with
        $cleanQuery = array();
        $cleanQuery[] = 'Collection';
        $cleanQuery[] = 'Ken';

        // query the database
        $results = $repo->communicationSearch($cleanQuery);

        // narrow the searches so we only return exactlly what we want
        $narrowedSearches = $searchNarrower->narrower($results, $cleanQuery, new Communication());

        // Assert that the size of the initial query is greater than the size of the narrowed query
        $this->assertTrue(sizeof($narrowedSearches) < sizeof($results));
    }

    /**
     * Story 11c
     * test that the search will work when an Date is specified
     */
    public function testSearchOnDate()
    {
        // create a new Repository to be used later
        $repo = $this->em->getRepository(Communication::class);

        // create an array with values to search with
        $cleanQuery = array();
        $cleanQuery[] = '2018-01-01';

        // query the database
        $results = $repo->CommunicationSearch($cleanQuery);

        // Assert that size of the query returns the expected number of results
        $this->assertEquals(3, sizeof($results));
    }

    /**
     * Story 11c
     * test that the search will work when an Type is specified
     */
    public function testSearchOnType()
    {
        // create a new Repository to be used later
        $repo = $this->em->getRepository(Communication::class);

        // create an array with values to search with
        $cleanQuery = array();
        $cleanQuery[] = 'Phone';

        // query the database
        $results = $repo->CommunicationSearch($cleanQuery);

        // Assert that size of the query returns the expected number of results
        $this->assertEquals(3, sizeof($results));
    }

    /**
     * Story 11c
     * test that the search will work when an Medium is specified
     */
    public function testSearchOnMedium()
    {
        // create a new Repository to be used later
        $repo = $this->em->getRepository(Communication::class);

        // create an array with values to search with
        $cleanQuery = array();
        $cleanQuery[] = 'Incoming';

        // query the database
        $results = $repo->CommunicationSearch($cleanQuery);

        // Assert that size of the query returns the expected number of results
        $this->assertEquals(3, sizeof($results));
    }

    /**
     * Story 11c
     * test that the search will work when an Contact Name is specified
     */
    public function testSearchOnContactName()
    {
        // create a new Repository to be used later
        $repo = $this->em->getRepository(Communication::class);

        // create an array with values to search with
        $cleanQuery = array();
        $cleanQuery[] = 'Ken';

        // query the database
        $results = $repo->CommunicationSearch($cleanQuery);

        // Assert that size of the query returns the expected number of results
        $this->assertEquals(1, sizeof($results));
    }

    /**
     * Story 11c
     * test that the search will work when an Contact Email is specified
     */
    public function testSearchOnContactEmail()
    {
        // create a new Repository to be used later
        $repo = $this->em->getRepository(Communication::class);

        // create an array with values to search with
        $cleanQuery = array();
        $cleanQuery[] = 'email@email.com';

        // query the database
        $results = $repo->CommunicationSearch($cleanQuery);

        // Assert that size of the query returns the expected number of results
        $this->assertEquals(2, sizeof($results));
    }

    /**
     * Story 11c
     * test that the search will work when an Contact Phone is specified
     */
    public function testSearchOnContactPhone()
    {
        // create a new Repository to be used later
        $repo = $this->em->getRepository(Communication::class);

        // create an array with values to search with
        $cleanQuery = array();
        $cleanQuery[] = '111-111-1111';

        // query the database
        $results = $repo->CommunicationSearch($cleanQuery);

        // Assert that size of the query returns the expected number of results
        $this->assertEquals(2, sizeof($results));
    }

    /**
     * Story 11c
     * test that a user can search for a Communication based on a field in its associated Property
     */
    public function testSearchOnProperty()
    {
        // create a new Repository to be used later
        $repo = $this->em->getRepository(Communication::class);

        // create an array with values to search with
        $cleanQuery = array();
        $cleanQuery[] = 'Cosmo';

        // query the database
        $results = $repo->CommunicationSearch($cleanQuery);

        // Assert that size of the query returns the expected number of results
        $this->assertEquals(3, sizeof($results));
    }

    /**
     * Story 11c
     * test that the search will work when an Category is specified
     */
    public function testSearchOnCategory()
    {
        // create a new Repository to be used later
        $repo = $this->em->getRepository(Communication::class);

        // create an array with values to search with
        $cleanQuery = array();
        $cleanQuery[] = 'Collection';

        // query the database
        $results = $repo->CommunicationSearch($cleanQuery);

        // Assert that size of the query returns the expected number of results
        $this->assertEquals(2, sizeof($results));
    }

    /**
     * Story 11c
     * test that the search will work when an Description is specified
     */
    public function testSearchOnDescription()
    {
        // create a new Repository to be used later
        $repo = $this->em->getRepository(Communication::class);

        // create an array with values to search with
        $cleanQuery = array();
        $cleanQuery[] = "Its a bin";

        // query the database
        $results = $repo->CommunicationSearch($cleanQuery);

        // Assert that size of the query returns the expected number of results
        $this->assertEquals(3, sizeof($results));
    }



    public function testSearchUsingForwardSlash()
    {
        // create a new Repository to be used later
        $repo = $this->em->getRepository(Communication::class);

        // create an array with values to search with
        $cleanQuery = array();
        $cleanQuery[] = "N/A";

        // query the database
        $results = $repo->CommunicationSearch($cleanQuery);

        // Assert that size of the query returns the expected number of results
        $this->assertEquals(1, sizeof($results));
    }

    public function testSearchUsingBackSlash()
    {
        // A cleaner to help pass a back-slash to the repository
        $cleaner = new Cleaner();

        // create a new Repository to be used later
        $repo = $this->em->getRepository(Communication::class);

        // create an array of values to search for using cleaner and a back-slash
        $cleanQuery = $cleaner->cleanSearchQuery(" \ ");

        // query the database
        $results = $repo->CommunicationSearch($cleanQuery);

        // Assert that size of the query returns the expected number of results
        $this->assertEquals(1, sizeof($results));
    }

    /**
     * {@inheritDoc}
     */
    protected function tearDown()
    {
        parent::tearDown();

        // Delete everything out of the property table after inserting stuff
        $stmt = $this->em->getConnection()->prepare('DELETE FROM Communication');
        $stmt->execute();

        $stmt = $this->em->getConnection()->prepare('DELETE FROM Property');
        $stmt->execute();

        $stmt = $this->em->getConnection()->prepare('DELETE FROM Address');
        $stmt->execute();

        $this->em->close();
        $this->em = null; // avoid memory leaks
    }

}