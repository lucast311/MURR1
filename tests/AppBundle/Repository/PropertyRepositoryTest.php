<?php

namespace Tests\AppBundle\Repository;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use AppBundle\Entity\Property;
use AppBundle\Entity\Address;
use AppBundle\DataFixtures\ORM\LoadPropertyData;
use AppBundle\Services\SearchNarrower;
use Tests\AppBundle\DatabasePrimer;

class PropertyRepositoryTest extends KernelTestCase
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

        $propertyLoader = new LoadPropertyData();
        $propertyLoader->load($this->em);
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
        $id = $repository->save($property);
        //Assert that the id was returned
        $this->assertNotNull($id);
        //check the contact id is the same as the returned id
        $this->assertEquals($property->getId(), $id);
    }

    /**
     * This function will test the update functionality of the repository
     * Story 4C User edits a property
     */
    public function testUpdate()
    {
        //Insert a property into the database
        $property = new Property();
        $property->setSiteId(1593844);
        $property->setPropertyName("Charlton Arms");
        $property->setPropertyType("Townhouse Condo");
        $property->setPropertyStatus("Active");
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

        //get the repository
        $repository = $this->em->getRepository(Property::class);
        //Call insert on the repository and record the id of the new object

        //insert
        $id = $repository->save($property);

        //Make a change to the property object
        $property->setPropertyStatus('Inactive (Renovation)');

        //Call the update function on the property
        $repository->save($property);

        //Get the supposedly updated property from the database
        $dbProperty = $repository->findOneById($id);

        //check if the updated property contains the edited field
        $this->assertEquals('Inactive (Renovation)', $dbProperty->getPropertyStatus());
    }



    /**
     * story 4d
     * test that Property objects are returned by the search
     */
    public function testPropertyObjectsReturned()
    {
        // get a repository to search with
        $repo = $this->em->getRepository(Property::class);

        // create an array with values to search with
        $searches = array();
        $searches[] = 'Charlton';
        $searches[] = 'Arms';

        // query the database
        $results = $repo->propertySearch($searches);

        // create a new ReflectionClass object, using the returned object at index 0
        $resultReflection = new \ReflectionClass(get_class($results[0]));

        // Assert that the name of the Reflection object is 'Property'
        $this->assertTrue($resultReflection->getShortName() == 'Property');
    }

    /**
     * Story 4d
     * test that the SearchNarrower actually reduces rthe number of results from the initial query
     */
    public function testSearchNarrowerFunctionality()
    {
        // create a new SearchNarrower to be used later
        $searchNarrower = new SearchNarrower();

        // get a repository to search with
        $repo = $this->em->getRepository(Property::class);

        // create an array with values to search with
        $cleanQuery = array();
        $cleanQuery[] = 'Charlton';
        $cleanQuery[] = 'Townhouse';

        // query the database
        $results = $repo->propertySearch($cleanQuery);

        // narrow the searches so we only return exactlly what we want
        $narrowedSearches = $searchNarrower->narrower($results, $cleanQuery, new Property());

        // Assert that the size of the initial query is greater than the size of the narrowed query
        $this->assertTrue(sizeof($narrowedSearches) < sizeof($results));
    }

    /**
     * Story 4d
     * test that the search will work when an Address is specified
     */
    public function testSearchOnAddress()
    {
        // create a new SearchNarrower to be used later
        $repo = $this->em->getRepository(Property::class);

        // create an array with values to search with
        $cleanQuery = array();
        $cleanQuery[] = 'Saskatoon';

        // query the database
        $results = $repo->propertySearch($cleanQuery);

        // Assert that size of the query returns the expected number of results
        $this->assertEquals(16, sizeof($results));
    }

    ///**
    // * Story 4h
    // * Tests that the getAllContainers method in the PropertyRepository returns a list of containers for a property given a property id
    // */
    //public function testGetAllContainers()
    //{
    //    // Create a valid property
    //    $property = new Property();
    //    $property->setSiteId(1593844);
    //    $property->setPropertyName("Charlton Arms");
    //    $property->setPropertyType("Townhouse Condo");
    //    $property->setPropertyStatus("Active");
    //    $property->setStructureId(54586);
    //    $property->setNumUnits(5);
    //    $property->setNeighbourhoodName("Sutherland");
    //    $property->setNeighbourhoodId("O48");

    //    // Have to create a new valid address too otherwise doctrine will fail
    //    $address = new Address();
    //    $address->setStreetAddress("12 15th st east");
    //    $address->setPostalCode("S0E1A0");
    //    $address->setCity("Saskatoon");
    //    $address->setProvince("Saskatchewan");
    //    $address->setCountry("Canada");
    //    $property->setAddress($address);


    //    // WE WILL NEED TO GO INTO PROPERTY AND ADD FUNCTIONALITY TO THE setBins($bins) METHOD
    //    // IF WE DON'T, THEN WE HAVE NO WAY OF LINKING A PROPERTY TO A LIST OF BINS IN THE CODE


    //    $container = new Container();
    //    $container->setContainerSerial("W114-320-001");
    //    $container->setType("Bin");
    //    $container->setSize("6 yd");

    //    // HOW DOES setPickUpInfo() WORK. IT SAYS THAT IT TAKES IN AN INTEGER IN THE ENTITY CLASS.
    //    $container->setPickUpInfo();

    //    $container->setIsInaccessable(true);
    //    $container->setIsContaminated(false);
    //    $container->setIsGraffiti(false);

    //    // Get the repository
    //    $repository = $this->em->getRepository(Property::class);

    //    // Insert the property
    //    $propertyId = $repository->save($property);

    //    // Insert the container
    //    $repository->save($container);

    //    // Add the bin to an array that we will loop through and add to the property
    //    $bins = array($container);

    //    // Link the container to the property (THIS METHOD HAS NOT BEEN IMPLEMENTED YET)
    //    $property->setBins($bins);

    //    //Call getAllContainers to get an array of all containers associated to that property
    //    $propertyBins = $repository->getAllContainers($propertyId);

    //    $this->assertTrue($propertyBins == $bins);
    //}

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
        $stmt = $this->em->getConnection()->prepare("DELETE FROM Address");
        $stmt->execute();

        $this->em->close();
        $this->em = null;//avoid memory meaks
    }
}