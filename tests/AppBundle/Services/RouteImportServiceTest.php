<?php
use AppBundle\Entity\Container;
use AppBundle\Entity\Route;
use AppBundle\Entity\RoutePickup;
use AppBundle\Repository\RouteRepository;
use AppBundle\Repository\RoutePickupRepository;


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
    *
    * 22a 
    * tests the csvToRoutePickup method to make sure it is catching invalid formats
    */
    function testInvalidFileFormat()
    {
        // Pass in a csv that doesn't match a container in the system

        // Assert that the invalidFileFormat is true
    }

   /**
    *
    * 22a 
    * tests the createRouteFromFile method to ensure duplicate serials are caught
    */
    function testDuplicateSerials()
    {
        // call the csvToRoutePickup method and pass in the csv file object

        // check if the duplicateSerials variable is true
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

        // test to see if routeSerials were passed back
    }
}