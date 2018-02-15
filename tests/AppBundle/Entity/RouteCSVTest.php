<?php
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use AppBundle\Entity\RouteCSV;

/**
 * RouteCSVTest short summary.
 *
 * RouteCSVTest description.
 *
 * @version 1.0
 * @author CST220
 */
class RouteCSVTest extends KernelTestCase
{

    private $routeCSV;
    protected function setUp()
    {
        self::bootKernel();
        $this->routeCSV = new RouteCSV();
        $this->routeCSV->setRouteId(1001);

        // Retrieves a test file and puts it into a file variable
        $path = $this->get('kernel')->getRootDir() . '/../tests/SampleData/Routes/valid_route.csv';
        $file = fopen($path, "r") or die("can't open file");

        // Set the RouteCSVs file attribute
        $this->routeCSV->setFile($file);

        $this->validator = static::$kernel->getContainer()->get("validator");

    }

   /**
    * Tests to make sure the a valid RouteCSV is in fact a valid RouteCSV
    */
    protected function validRouteCSV()
    {
        $error = $this->validator->validate($this->collectionHistory);

        $this->assertEquals(0, count($error));
    }

   /**
    * Tests to make sure the routeId is an integer
    */
    protected function inValidRouteCSVStringID()
    {
        $error = $this->validator->validate($this->collectionHistory);

        $this->routeCSV->setRouteId("one");

        $this->assertEquals(1, count($error));
        $this->assertEquals($errors[0], "Please enter an integer.");
    }

   /**
    * Tests to make sure the uploaded file isn't too large
    */
    protected function inValidRouteCSVFileTooLarge()
    {
        $error = $this->validator->validate($this->collectionHistory);

        // Retrieves a test file and puts it into a file variable
        $path = $this->get('kernel')->getRootDir() . '/../tests/SampleData/Routes/invalid_route_Too_Large.csv';
        $file = fopen($path, "r") or die("can't open file");
        $this->routeCSV->setFile($file);

        $this->assertEquals(1, count($error));
        $this->assertEquals($errors[0], "The max route size is 1 MB.");
    }

   /**
    * Tests to make sure the uploaded file is the correct mimetype
    */
    protected function inValidRouteCSVMimeType()
    {
        $error = $this->validator->validate($this->collectionHistory);


        // Retrieves a test file and puts it into a file variable
        $path = $this->get('kernel')->getRootDir() . '/../tests/SampleData/Routes/invalid_route_type.txt';
        $file = fopen($path, "r") or die("can't open file");
        $this->routeCSV->setFile($file);

        $this->assertEquals(1, count($error));
        $this->assertEquals($errors[0], "Invalid route file type. Please upload a valid route CSV.");
    }

   /**
    * Tests Both RouteCSV fields to make sure the errors work
    */
    protected function inValidRouteCSVAllInvalidFields()
    {
        $error = $this->validator->validate($this->collectionHistory);

        // Change the routeId to something invalid
        $this->routeCSV->setRouteId("two");

        // Retrieves a test file and puts it into a file variable
        $path = $this->get('kernel')->getRootDir() . '/../tests/SampleData/Routes/invalid_route_type.txt';
        $file = fopen($path, "r") or die("can't open file");
        $this->routeCSV->setFile($file);

        $this->assertEquals(2, count($error));
        $this->assertEquals($errors[0], "Please enter an integer.");
        $this->assertEquals($errors[1], "Invalid route file type. Please upload a valid route CSV.");
    }
}