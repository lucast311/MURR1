<?php
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use AppBundle\Entity\Container;
use AppBundle\Entity\Route;
use AppBundle\Entity\RoutePickup;
use AppBundle\Repository\RouteRepository;
use AppBundle\Repository\RoutePickupRepository;
use AppBundle\Services\RouteImportService;

/**
 * RouteImportServiceTest short summary.
 *
 * RouteImportServiceTest description.
 *
 * @version 1.0
 * @author cst206
 */
class RouteImportServiceTest extends KernelTestCase
{
     /**
     * The entity manager
     * @var \Doctrine\ORM\EntityManager
     */
    private $em;

   /**
    *
    * 22a
    * tests the csvToRoutePickup method to make sure it is catching invalid formats
    */
    function testInvalidFileFormat()
    {
        // Pass in a csv that doesn't match a container in the system
        $path = $this->get('kernel')->getRootDir() . '/../tests/SampleData/Routes/invalid_route.jpeg.csv';
        $file = fopen($path, "r") or die("can't open file");

        // Assert that the invalidFileFormat is true
        $this->assertTrue((new RouteImportService)->createRouteFromFile(649, $file)[0]);
    }

   /**
    *
    * 22a
    * tests the createRouteFromFile method to ensure duplicate serials are caught
    */
    function testDuplicateSerials()
    {
        // call the csvToRoutePickup method and pass in the csv file object
        $path = $this->get('kernel')->getRootDir() . '/../tests/SampleData/Routes/invalid_route_duplicate_data.csv';
        $file = fopen($path, "r") or die("can't open file");

        // check if the duplicateSerials variable is true
        $this->assertTrue((new RouteImportService)->createRouteFromFile(649, $file)[2]);
    }

   /**
    *
    * 22a
    * tests the csvToRoutePickup method to make sure it is creating RoutePickup objects
    * properly.
    */
    function testCsvToRoutePickup()
    {
        // Pass in a valid csv bin serials in it
        $path = $this->get('kernel')->getRootDir() . '/../tests/SampleData/Routes/valid_route.csv';
        $file = fopen($path, "r") or die("can't open file");

        $csvContents = fread($file,filesize($path));
        // test to see if routeSerials were passed back
        $this->assertTrue(sizeof((new RouteImportService)->csvToRoutePickup($csvContents)) > 1);
    }
}