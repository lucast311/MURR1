<?php
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use AppBundle\Entity\RoutePickup;
use AppBundle\Entity\Route;

/**
 * RoutePickupRepositoryTest short summary.
 *
 * RoutePickupRepositoryTest description.
 *
 * @version 1.0
 * @author cst244
 */
class RoutePickupRepositoryTest extends KernelTestCase
{
    /**
     * The entity manager
     * @var \Doctrine\ORM\EntityManager
     */
    private $em;

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
     * Story 22b
     * Tests that a routePickup is able to be stored in the database
     */
    public function testSave(){

        $route = new Route();
        $route->setRouteId(1001);

        $routePickup = new RoutePickup();
        $routePickup->setPickupOrder(1);

        //Get the repository for testing
        $repository = $this->em->getRepository(RoutePickup::class);
        //Call insert on the repository and record the id of the new object
        $id = $repository->save($routePickup);
        //Assert that the id was returned
        $this->assertNotNull($id);
        //check the Route Pickup id is the same as the returned id
        $this->assertEquals($routePickup->getId(), $id);
    }
}