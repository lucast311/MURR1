<?php

namespace Tests\AppBundle\Repository;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use AppBundle\Entity\Container;
use AppBundle\Services\SearchNarrower;
use AppBundle\Services\Cleaner;
use AppBundle\DataFixtures\ORM\LoadContainerData;
use AppBundle\DataFixtures\ORM\LoadRouteData;
use Tests\AppBundle\DatabasePrimer;

/**
 * ContainerRepositoryTest short summary.
 *
 * ContainerRepositoryTest description.
 *
 * @version 1.0
 * @author cst201
 */
class ContainerRepositoryTest extends KernelTestCase
{
    private $em;

    public static function setUpBeforeClass()
    {
        self::bootKernel();
        DatabasePrimer::prime(self::$kernel);
    }


    protected function setUp()
    {
        self::bootKernel();

        $this->em = static::$kernel->getContainer()
            ->get('doctrine')
            ->getManager();

        $containerLoader = new LoadContainerData();
        $containerLoader->load($this->em);

        $routeLoader = new LoadRouteData();
        $routeLoader->load($this->em);

    }

    public function testSave()
    {
        $container = new Container();
        $container->setContainerSerial("testSerialRepo");
        $container->setType("Bin");
        $container->setSize("6");
        $container->setStatus("Active");

        $repo = $this->em->getRepository(Container::class);

        $id = $repo->save($container);
        $this->assertNotNull($id);
        $this->assertEquals($container->getId(), $id);

        $repo->remove($container);

    }

    /**
     * Story 12d
     * test that a Container can be searched for using only one specified string
     */
    public function testSearchByOnlyOneField()
    {
        // create a new Repository to be used later
        $repo = $this->em->getRepository(Container::class);

        // create an array with values to search with
        $cleanQuery = array();
        $cleanQuery[] = 'Bin';

        // query the database
        $results = $repo->containerSearch($cleanQuery);

        // Assert that size of the query returns the expected number of results
        $this->assertEquals(3, sizeof($results));
    }

    /**
     * Story 12d
     * test that the search narrower functions
     */
    public function testSearchNarrowerFunctionality()
    {
        // create a new Repository to be used later
        $searchNarrower = new SearchNarrower();

        // get a repository to search with
        $repo = $this->em->getRepository(Container::class);

        // create an array with values to search with
        $cleanQuery = array();
        $cleanQuery[] = 'Bin';
        $cleanQuery[] = 'Active';

        // query the database
        $results = $repo->containerSearch($cleanQuery);

        // narrow the searches so we only return exactlly what we want
        $narrowedSearches = $searchNarrower->narrower($results, $cleanQuery, new Container());

        // Assert that the size of the initial query is greater than the size of the narrowed query
        $this->assertTrue(sizeof($narrowedSearches) < sizeof($results));
    }

    /**
     * Story 12d
     * Test that a container object is returned
     */
    public function testContainerObjectsReturned()
    {
        // get a repository to search with
        $repo = $this->em->getRepository(Container::class);

        // create an array with values to search with
        $searches = array();
        $searches[] = 'Bin';

        // query the database
        $results = $repo->containerSearch($searches);

        $this->AssertTrue(is_a($results[0],Container::class));
    }



    ///////////////////////////////////////////////////////
    // WE DON'T KNOW WHAT A FREQUENCY IS STORED AS
    ///////////////////////////////////////////////////////





    /**
     * Story 12d
     * test that the search will work when an frequency is specified
     */
    public function testSearchOnFrequency()
    {
        // create a new Repository to be used later
        $repo = $this->em->getRepository(Container::class);

        // create an array with values to search with
        $cleanQuery = array();
        $cleanQuery[] = 'weekly';

        // query the database
        $results = $repo->containerSearch($cleanQuery);

        // Assert that size of the query returns the expected number of results
        $this->assertEquals(12, sizeof($results));
    }

    /**
     * Story 12d
     * test that the search will work when an ContainerSerial is specified
     */
    public function testSearchOnTContainerSerial()
    {
        // create a new Repository to be used later
        $repo = $this->em->getRepository(Container::class);

        // create an array with values to search with
        $cleanQuery = array();
        $cleanQuery[] = '123457';

        // query the database
        $results = $repo->containerSearch($cleanQuery);

        // Assert that size of the query returns the expected number of results
        $this->assertEquals(1, sizeof($results));
    }

    /**
     * Story 12d
     * test that the search will work when an Location Description is specified
     */
    public function testSearchOnLocationDescription()
    {
        // create a new Repository to be used later
        $repo = $this->em->getRepository(Container::class);

        // create an array with values to search with
        $cleanQuery = array();
        $cleanQuery[] = 'South-west';
        $cleanQuery[] = 'side';

        // query the database
        $results = $repo->containerSearch($cleanQuery);

        // Assert that size of the query returns the expected number of results
        $this->assertEquals(12, sizeof($results));
    }

    /**
     * Story 12d
     * test that the search will work when an Logitude is specified
     */
    public function testSearchOnLogitude()
    {
        // create a new Repository to be used later
        $repo = $this->em->getRepository(Container::class);

        // create an array with values to search with
        $cleanQuery = array();
        $cleanQuery[] = '87';

        // query the database
        $results = $repo->containerSearch($cleanQuery);

        // Assert that size of the query returns the expected number of results
        $this->assertEquals(11, sizeof($results));
    }

    /**
     * Story 12d
     * test that the search will work when an Latitude is specified
     */
    public function testSearchOnLatitude()
    {
        // create a new Repository to be used later
        $repo = $this->em->getRepository(Container::class);

        // create an array with values to search with
        $cleanQuery = array();
        $cleanQuery[] = '88';

        // query the database
        $results = $repo->containerSearch($cleanQuery);

        // Assert that size of the query returns the expected number of results
        $this->assertEquals(12, sizeof($results));
    }

    /**
     * Story 12d
     * test that the search will work when an Type is specified
     */
    public function testSearchOnType()
    {
        // create a new Repository to be used later
        $repo = $this->em->getRepository(Container::class);

        // create an array with values to search with
        $cleanQuery = array();
        $cleanQuery[] = 'Bin';

        // query the database
        $results = $repo->containerSearch($cleanQuery);

        // Assert that size of the query returns the expected number of results
        $this->assertEquals(3, sizeof($results));
    }

    /**
     * Story 12d
     * test that the search will work when an Size is specified
     */
    public function testSearchOnSize()
    {
        // create a new Repository to be used later
        $repo = $this->em->getRepository(Container::class);

        // create an array with values to search with
        $cleanQuery = array();
        $cleanQuery[] = '6';
        $cleanQuery[] = 'yd';

        // query the database
        $results = $repo->containerSearch($cleanQuery);

        // Assert that size of the query returns the expected number of results
        $this->assertEquals(15, sizeof($results));
    }

    /**
     * Story 12d
     * test that the search will work when an Status is specified
     */
    public function testSearchOnStatus()
    {
        // create a new Repository to be used later
        $repo = $this->em->getRepository(Container::class);

        // create an array with values to search with
        $cleanQuery = array();
        $cleanQuery[] = 'Active';

        // query the database
        $results = $repo->containerSearch($cleanQuery);

        // Assert that size of the query returns the expected number of results
        $this->assertEquals(15, sizeof($results));
    }

    /**
     * Story 12d
     * test that the search will work when an Augmentation is specified
     */
    public function testSearchOnAugmentation()
    {
        // create a new Repository to be used later
        $repo = $this->em->getRepository(Container::class);

        // create an array with values to search with
        $cleanQuery = array();
        $cleanQuery[] = 'Wheels';

        // query the database
        $results = $repo->containerSearch($cleanQuery);

        // Assert that size of the query returns the expected number of results
        $this->assertEquals(11, sizeof($results));
    }

    /**
     * Story 12d
     * test that a user can search for a Container based on a field in its associated Property
     */
    public function testSearchOnProperty()
    {
        // create a new Repository to be used later
        $repo = $this->em->getRepository(Container::class);

        // create an array with values to search with
        $cleanQuery = array();
        $cleanQuery[] = 'Cosmo';

        // query the database
        $results = $repo->containerSearch($cleanQuery);

        // Assert that size of the query returns the expected number of results
        $this->assertEquals(11, sizeof($results));
    }

    /**
     * Story 12d
     * test that a user can search for a Container based on a field in its associated Structure
     */
    public function testSearchOnStructure()
    {
        // create a new Repository to be used later
        $repo = $this->em->getRepository(Container::class);

        // create an array with values to search with
        $cleanQuery = array();
        $cleanQuery[] = 'Hello';
        $cleanQuery[] = 'World!';

        // query the database
        $results = $repo->containerSearch($cleanQuery);

        // Assert that size of the query returns the expected number of results
        $this->assertEquals(2, sizeof($results));
    }

    /**
     * Story 12d
     * test what happens when the system receives a forward-slash to the search query
     */
    public function testSearchUsingForwardSlash()
    {
        // create a new Repository to be used later
        $repo = $this->em->getRepository(Container::class);

        // create an array with values to search with
        $cleanQuery = array();
        $cleanQuery[] = "N/A";

        // query the database
        $results = $repo->containerSearch($cleanQuery);

        // Assert that size of the query returns the expected number of results
        $this->assertEquals(0, sizeof($results));
    }

    /**
     * Story 12d
     * test what happens when the system receives a back-slash to the search query
     */
    public function testSearchUsingBackSlash()
    {
        // A cleaner to help pass a back-slash to the repository
        $cleaner = new Cleaner();

        // create a new Repository to be used later
        $repo = $this->em->getRepository(Container::class);

        // create an array of values to search for using cleaner and a back-slash
        $cleanQuery = $cleaner->cleanSearchQuery(" \ ");

        // query the database
        $results = $repo->containerSearch($cleanQuery);

        // Assert that size of the query returns the expected number of results
        $this->assertEquals(0, sizeof($results));
    }

     /**
     * (@inheritDoc)
     */

    protected function tearDown()
    {
        parent::tearDown();

        $stmt = $this->em->getConnection()->prepare("DELETE FROM Container");
        $stmt->execute();
        $stmt = $this->em->getConnection()->prepare("DELETE FROM Address");
        $stmt->execute();
        $stmt = $this->em->getConnection()->prepare("DELETE FROM Property");
        $stmt->execute();
        $stmt = $this->em->getConnection()->prepare("DELETE FROM Route");
        $stmt->execute();
        $stmt = $this->em->getConnection()->prepare("DELETE FROM User");
        $stmt->execute();

        $this->em->close();
        $this->em = null;//avoid memory meaks

    }

}