<?php
namespace Tests\AppBundle\Repository;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use AppBundle\Entity\Route;
use Tests\AppBundle\DatabasePrimer;

/**
 * RouteRepositoryTest short summary.
 *
 * RouteRepositoryTest description.
 *
 * @version 1.0
 * @author cst244
 */
class RouteRepositoryTest extends KernelTestCase
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
    }

    /**
     * Story 22b
     * Tests that the route is able to be inserted into the database
     */
    public function testSave(){

        $route = new Route();
        $route->setRouteId(1001);

        //Get the repository for testing
        $repository = $this->em->getRepository(Route::class);
        //Call insert on the repository and record the id of the new object
        $id = $repository->save($route);
        //Assert that the id was returned
        $this->assertNotNull($id);
        //check the route id is the same as the returned id
        $this->assertEquals($route->getId(), $id);
    }

}