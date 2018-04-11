<?php
namespace Tests\AppBundle\Controller;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use AppBundle\Entity\Route;
use AppBundle\Entity\Container;
use AppBundle\Entity\RoutePickup;
use AppBundle\DataFixtures\ORM\LoadUserData;
use Tests\AppBundle\DatabasePrimer;

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

        // Load the admin user into the database so they can log in
        $encoder = static::$kernel->getContainer()->get('security.password_encoder');

        $userLoader = new LoadUserData($encoder);
        $userLoader->load($this->em);
    }

    /**
     * Story 22b
     * Tests that you can add a container to the route and it shows up on the page
     */
    public function testAddRoute(){
        //create a route to add the pickup to
        $route = new Route();
        $route->setRouteId(1002);

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
        $client = static::createClient(array(), array('PHP_AUTH_USER' => 'admin', 'PHP_AUTH_PW'   => 'password'));
        $client->followRedirects(true);

        $crawler = $client->request('GET', '/route/manage/1');

        $form = $crawler->selectButton('Add')->form();
        $form["appbundle_routepickup[pickupOrder]"] = 1;
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
        $client = static::createClient(array(), array('PHP_AUTH_USER' => 'admin', 'PHP_AUTH_PW'   => 'password'));

        $crawler = $client->request('GET', '/route/manage/1');

        $form = $crawler->selectButton('Add')->form();
        $form["appbundle_routepickup[pickupOrder]"] = 1; //set the pickup order to be 1 instead of the other container previously inserted
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
        $client = static::createClient(array(), array('PHP_AUTH_USER' => 'admin', 'PHP_AUTH_PW'   => 'password'));

        $crawler = $client->request('GET', '/route/manage/1');

        $form = $crawler->selectButton('Add')->form();
        $form["appbundle_routepickup[pickupOrder]"] = 1000; //put a pickup order much farther beyond the number of bins
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
        $client = static::createClient(array(), array('PHP_AUTH_USER' => 'admin', 'PHP_AUTH_PW'   => 'password'));

        $crawler = $client->request('GET', '/route/manage/1');

        $form = $crawler->selectButton('Add')->form();
        $form["appbundle_routepickup[pickupOrder]"] = 1;
        $form["appbundle_routepickup[container]"] = 1; //select the first container in the list so that it is attempting to add it again

        $crawler = $client->submit($form);

        //Check that the required error message is on the page
        $this->assertContains("This container already exists in this route", $client->getResponse()->getContent());
    }

    /**
     * Story 22c
     * Tests that a RoutePickup can be removed from a route
     */
    public function testRemoveRoutePickup(){
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
        $client = static::createClient(array(), array('PHP_AUTH_USER' => 'admin', 'PHP_AUTH_PW'   => 'password'));
        $client->followRedirects(true); //follow any redirects (there will be some)

        //Request a removal of the RoutePickup with the ID of the RoutePickup to be removed
        $crawler = $client->request('GET', '/route/removecontainer/1');

        //Make make sure the table that would normally have the container does not apppear
        $this->assertNotContains("route_pickups", $crawler->html());
    }

    /**
     * Story 22c
     * Tests that when a Route is removed all the pickups after are decremented
     */
    public function testRemoveRoutePickupDecrement(){
        //create a route to add the pickup to
        $route = new Route();
        $route->setRouteId(1001);

        //Get the repository for the route
        $repository = $this->em->getRepository(Route::class);
        //Call insert on the repository for the route
        $repository->save($route);

        //specify a container for routePickup
        $container = new Container();
        $container->setContainerSerial("X111111");
        $container->setType("Bin");
        $container->setSize("6");
        $container->setStatus("Active");

        //specify a second container for this routePickup
        $container2 = new Container();
        $container2->setContainerSerial("X222222");
        $container2->setType("Bin");
        $container2->setSize("6");
        $container2->setStatus("Active");

        //save the container
        $repo = $this->em->getRepository(Container::class);
        $repo->save($container);
        $repo->save($container2);

        //create a pickup
        $rp = new RoutePickup();
        $rp->setPickupOrder(1);
        $rp->setRoute($route);
        $rp->setContainer($container);

        //create a second pickup
        $rp2 = new RoutePickup();
        $rp2->setPickupOrder(2);
        $rp2->setRoute($route);
        $rp2->setContainer($container2);

        //save the route pickups
        $repo = $this->em->getRepository(RoutePickup::class);
        $repo->save($rp);
        $repo->save($rp2);



        //get the client
        $client = static::createClient(array(), array('PHP_AUTH_USER' => 'admin', 'PHP_AUTH_PW'   => 'password'));
        $client->followRedirects(true);

        //Request a removal of the RoutePickup with the ID of the RoutePickup to be removed
        $crawler = $client->request('GET', '/route/removecontainer/1');


        //Check that the row that was previously the second is now the first
        $this->assertContains("1", $crawler->filter("table tr:contains('X222222')")->html());
    }

    /**
     * Story22c
     * Tests that if you try and request a removal of a non-existant pickup you'll be brought to an error page
     */
    public function testRemoveRoutePickupNonexistant(){
        //get the client
        $client = static::createClient(array(), array('PHP_AUTH_USER' => 'admin', 'PHP_AUTH_PW'   => 'password'));
        $client->followRedirects(true);

        //Request a removal of the RoutePickup with the ID of the RoutePickup to be removed
        //This pickup does not exist
        $client->request('GET', '/route/removecontainer/2092');

        //Check that the client is on the error page
        $this->assertContains("The specified container could not be removed from this route",$client->getResponse()->getContent());
    }
    /**
     * (@inheritDoc)
     */
    protected function tearDown()
    {
        parent::tearDown();
        $client = static::createClient();
        $em = $client->getContainer()->get('doctrine.orm.entity_manager');
        $stmt = $em->getConnection()->prepare('DELETE FROM RoutePickup');
        $stmt->execute();
        $stmt = $em->getConnection()->prepare('DELETE FROM Route');
        $stmt->execute();
        $stmt = $em->getConnection()->prepare('DELETE FROM Container');
        $stmt->execute();
        $stmt = $em->getConnection()->prepare('DELETE FROM User');
        $stmt->execute();
    }

}