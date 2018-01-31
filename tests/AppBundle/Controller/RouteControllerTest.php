<?php
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use AppBundle\Entity\Route;
use AppBundle\Entity\Container;
use AppBundle\Entity\RoutePickup;

/**
 * RouteControllerTest short summary.
 *
 * RouteControllerTest description.
 *
 * @version 1.0
 * @author cst244
 */
class RouteControllerTest extends WebTestCase
{

    private $em;

    protected function setUp()
    {
        self::bootKernel();
        $this->em = static::$kernel->getContainer()
            ->get('doctrine')
            ->getManager();
    }

    /**
     * Story 22b
     * Tests that you can add a container to the route and it shows up on the page
     */
    public function testAddRoute(){
        //create a route to add the pickup to
        $route = new Route();
        $route->setRouteId(1001);

        //Get the repository for the route
        $repository = $this->em->getRepository(Route::class);
        //Call insert on the repository for the route
        $repository->save($route);

        //specify a container for this routePickup
        $container = new Container();
        $container->setContainerSerial("X123456");
        $container->setType("Bin");
        $container->setSize("6");
        $container->setStatus("Active");

        //save the container
        $repo = $this->em->getRepository(Container::class);
        $repo->save($container);

        //get the client
        $client = static::createClient();

        $crawler = $client->request('GET', '/route/1');

        $form = $crawler->selectButton('Add')->form();
        $form["appbundle_routepickup[pickuporder]"] = 1;
        $form["appbundle_routepickup[container]"] = 1; //select the first container in the list

        $crawler = $client->submit($form);

        //check that the containers serial is on the page
        $this->assertContains("X123456",$client->getResponse()->getContent());
        //check that the table contains the serial
        $this->assertContains("X123456", $crawler->filter("table")->html());
    }

    /**
     * Story 22b
     * Tests that you can add a container to the route and it increments the orders of the other containers
     */
    public function testInsertRoute(){
        //create a route to add the pickup to
        $route = new Route();
        $route->setRouteId(1001);

        //Get the repository for the route
        $repository = $this->em->getRepository(Route::class);
        //Call insert on the repository for the route
        $repository->save($route);

        //specify a container for routePickup
        $container = new Container();
        $container->setContainerSerial("X11111");
        $container->setType("Bin");
        $container->setSize("6");
        $container->setStatus("Active");

        //specify a second container for this routePickup
        $container2 = new Container();
        $container2->setContainerSerial("X123456");
        $container2->setType("Bin");
        $container2->setSize("6");
        $container2->setStatus("Active");

        //save the container
        $repo = $this->em->getRepository(Container::class);
        $repo->save($container);
        $repo->save($container2);

        $rp = new RoutePickup();
        $rp->setPickupOrder(1);
        $rp->setRoute($route);
        $rp->setContainer($container);

        //save the route pickup
        $repo = $this->em->getRepository(RoutePickup::class);
        $repo->save($rp);



        //get the client
        $client = static::createClient();

        $crawler = $client->request('GET', '/route/1');

        $form = $crawler->selectButton('Add')->form();
        $form["appbundle_routepickup[pickuporder]"] = 1; //set the pickup order to be 1 instead of the other container previously inserted
        $form["appbundle_routepickup[container]"] = 2; //select the second container in the list

        $crawler = $client->submit($form);


        //Check that the row with the previous bin has a pickup order of 2
        $this->assertContains("2", $crawler->filter("table tr:contains('X11111')")->html());
    }

    /**
     * Story 22b
     * Tests that if you add a container to the route in a pickup order beyond the number of bins, it will be put in the last place
     */
    public function testInsertBeyond(){
        //create a route to add the pickup to
        $route = new Route();
        $route->setRouteId(1001);

        //Get the repository for the route
        $repository = $this->em->getRepository(Route::class);
        //Call insert on the repository for the route
        $repository->save($route);

        //specify a container for this routePickup
        $container = new Container();
        $container->setContainerSerial("X222222");
        $container->setType("Bin");
        $container->setSize("6");
        $container->setStatus("Active");

        //save the container
        $repo = $this->em->getRepository(Container::class);
        $repo->save($container);

        //get the client
        $client = static::createClient();

        $crawler = $client->request('GET', '/route/1');

        $form = $crawler->selectButton('Add')->form();
        $form["appbundle_routepickup[pickuporder]"] = 1000; //put a pickup order much farther beyond the number of bins
        $form["appbundle_routepickup[container]"] = 1; //select the first container in the list

        $crawler = $client->submit($form);


        //check that the containers row has the pickup order of 1
        $this->assertContains("1", $crawler->filter("table tr:contains('X222222')")->html());
    }

    /**
     * Story 22b
     * Tests that if you attempt to insert the same container on the route it will give an error message
     */
    public function testInsertExisting(){
        //create a route to add the pickup to
        $route = new Route();
        $route->setRouteId(1001);

        //Get the repository for the route
        $repository = $this->em->getRepository(Route::class);
        //Call insert on the repository for the route
        $repository->save($route);

        //specify a container for routePickup
        $container = new Container();
        $container->setContainerSerial("X11111");
        $container->setType("Bin");
        $container->setSize("6");
        $container->setStatus("Active");

        //save the container
        $repo = $this->em->getRepository(Container::class);
        $repo->save($container);

        //Create a route pickup for this container
        $rp = new RoutePickup();
        $rp->setPickupOrder(1);
        $rp->setRoute($route);
        $rp->setContainer($container);

        //save the route pickup
        $repo = $this->em->getRepository(RoutePickup::class);
        $repo->save($rp);

        //get the client
        $client = static::createClient();

        $crawler = $client->request('GET', '/route/1');

        $form = $crawler->selectButton('Add')->form();
        $form["appbundle_routepickup[pickuporder]"] = 1; 
        $form["appbundle_routepickup[container]"] = 1; //select the first container in the list so that it is attempting to add it again

        $crawler = $client->submit($form);

        //Check that the required error message is on the page
        $this->assertContains("This container already exists in this route", $client->getResponse()->getContent());
    }

    /**
     * (@inheritDoc)
     */
    protected function tearDown()
    {
        parent::tearDown();
        $client = static::createClient();
        $em = $client->getContainer()->get('doctrine.orm.entity_manager');
        $stmt = $em->getConnection()->prepare('DELETE FROM Route');
        $stmt->execute();
        $stmt = $em->getConnection()->prepare('DELETE FROM Container');
        $stmt->execute();
        $stmt = $em->getConnection()->prepare('DELETE FROM RoutePickup');
        $stmt->execute();
    }

}