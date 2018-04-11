<?php
namespace tests\AppBundle\Entity;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use AppBundle\Entity\Route;

/**
 * RouteTest short summary.
 *
 * RouteTest description.
 *
 * @version 1.0
 * @author cst244
 */
class RouteTest extends KernelTestCase
{

    private $route;

    public function setUp()
    {
        self::bootKernel();

        $this->route = new Route();
        $this->route->setRouteId("1001");

        //$this->validator = Validation::createValidatorBuilder()->enableAnnotationMapping()->getValidator();
        // Gotta do some weird stuff because doing a validation on the class for unique
        $this->validator = static::$kernel->getContainer()->get("validator");
    }

    /**
     * Story 22b
     * Tests that a route can be valid
     */
    public function testRouteIdValid(){
        // Validate the route
        $error = $this->validator->validate($this->route);

        // Assert sure their are 0 errors
        $this->assertEquals(0, count($error));
    }

    /**
     * Story 22b
     * tests that a route id can not be negative
     */
    public function testRouteIdNegative(){
        //make the route invalid
        $this->route->setRouteId("-1000");

        // Validate the route
        $error = $this->validator->validate($this->route, null, array('route'));

        // Assert sure their are 1 errors
        $this->assertEquals(1, count($error));
        $this->assertEquals('The Route ID must contain 1 to 6 digits, no letters',$error[0]->getMessage());
    }

    /**
     * Story 22b
     * tests that a route id is required
     */
    public function testRouteIdMissing(){
        //make the route invalid
        $this->route->setRouteId(null);

        // Validate the route
        $error = $this->validator->validate($this->route, null, array('route'));

        // Assert sure their are 1 errors
        $this->assertEquals(1, count($error));
        $this->assertEquals('Please specify a Route ID',$error[0]->getMessage());
    }

}