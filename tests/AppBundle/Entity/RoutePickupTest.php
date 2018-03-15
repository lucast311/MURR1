<?php
namespace tests\AppBundle\Entity;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use AppBundle\Entity\Route;
use AppBundle\Entity\RoutePickup;
use AppBundle\Entity\Container;
use Tests\AppBundle\DatabasePrimer; 

/**
 * RoutePickupTest short summary.
 *
 * RoutePickupTest description.
 *
 * @version 1.0
 * @author cst244
 */
class RoutePickupTest extends KernelTestCase
{

    private $routePickup;

    public static function setUpBeforeClass()
    {
        self::bootKernel();
        DatabasePrimer::prime(self::$kernel);
    }


    public function setUp()
    {
        self::bootKernel();

        $route = new Route();
        $route->setRouteId(1001);

        $container = new Container();
        $container->setContainerSerial("testSerialRepo");
        $container->setType("Bin");
        $container->setSize("6");
        $container->setStatus("Active");

        //create a route pickup with a route and container
        $this->routePickup = new RoutePickup();
        $this->routePickup->setPickupOrder(1)
            ->setContainer($container)
            ->setRoute($route);

        //$this->validator = Validation::createValidatorBuilder()->enableAnnotationMapping()->getValidator();
        // Gotta do some weird stuff because doing a validation on the class for unique
        $this->validator = static::$kernel->getContainer()->get("validator");
    }

    /**
     * Story 22b
     * Tests that a route pickup can be valid
     */
    public function testRoutePickupValid(){
        // Validate the routePickup. It should have all valid properties
        $error = $this->validator->validate($this->routePickup);

        // Assert sure their are 0 errors
        $this->assertEquals(0, count($error));
    }

    /**
     * Story 22b
     * tests that a routepickup order must not be negative
     */
    public function testRoutePickupNegativeOrder(){
        //make the pickup order invalid
        $this->routePickup->setPickupOrder(-10);

        $error = $this->validator->validate($this->routePickup);

        // Assert sure their are 1 errors
        $this->assertEquals(1, count($error));
        $this->assertEquals('Pickup order must be greater than 0',$error[0]->getMessage());
    }

    /**
     * Story 22b
     * tests that a routepickup order must not be 0
     */
    public function testRoutePickupZeroOrder(){
        //make the pickup order invalid
        $this->routePickup->setPickupOrder(0);

        $error = $this->validator->validate($this->routePickup);

        // Assert sure their are 1 errors
        $this->assertEquals(1, count($error));
        $this->assertEquals('Pickup order must be greater than 0',$error[0]->getMessage());
    }

    /**
     * Story 22b
     * tests that a routePickup needs a pickup order
     */
    public function testRoutePickupMissingOrder(){
        //make the pickup order invalid
        $this->routePickup->setPickupOrder(null);

        $error = $this->validator->validate($this->routePickup);

        // Assert sure their are 1 errors
        $this->assertEquals(1, count($error));
        $this->assertEquals('Please specify a pickup order',$error[0]->getMessage());
    }

    /**
     * Story 22b
     * tests that a routePickup needs a route
     */
    public function testRoutePickupMissingRoute(){
        //make the pickup order invalid
        $this->routePickup->setRoute(null);

        $error = $this->validator->validate($this->routePickup);

        // Assert sure their are 1 errors
        $this->assertEquals(1, count($error));
        //Does not have a custom error message
    }

    /**
     * Story 22b
     * tests that a routePickup needs a Container
     */
    public function testRoutePickupMissingContainer(){
        //make the pickup order invalid
        $this->routePickup->setContainer(null);

        $error = $this->validator->validate($this->routePickup);

        // Assert sure their are 1 errors
        $this->assertEquals(1, count($error));
        //Does not have a custom error message
    }
}