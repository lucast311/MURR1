<?php
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use AppBundle\Entity\Truck;
use AppBundle\DataFixtures\ORM\LoadTruckData;
use Tests\AppBundle\DatabasePrimer;

/**
 * TruckControllerTest
 * tests for the truck controller
 *
 * @version 1.0
 * @author cst206
 */
class TruckControllerTest extends WebTestCase
{
    private $em;
    private $truck;

    public static function setUpBeforeClass()
    {
        self::bootKernel();
        DatabasePrimer::prime(self::$kernel);
    }

    /**
     * Just some setup stuff
     * {@inheritDoc}
     */
    protected function setUp()
    {
        self::bootKernel();

        $this->em = static::$kernel->getContainer()
            ->get('doctrine')
            ->getManager();

        $this->truck = (new Truck())
            ->setTruckId("00886")
            ->setType("Large");

        // Load the admin user into the database so they can log in
        $encoder = static::$kernel->getContainer()->get('security.password_encoder');

        $truckLoader = new LoadTruckData($encoder);
        $truckLoader->load($this->em);
    }

   /**
     * Story 40a
     * Tests that you can view a Truck in the list
     */
    public function testViewTruck()
    {
        //Get the truck repository
        $repository = $this->em->getRepository(Truck::class);
        //insert the truck to db
        $repository->save($this->truck);

        //get the client
        $client = static::createClient(array(), array('PHP_AUTH_USER' => 'admin', 'PHP_AUTH_PW'   => 'password'));
        $client->followRedirects(true);

        //go to truck utility page
        $crawler = $client->request('GET', '/trucks');

        //Check that the truck is displayed in the list
        $this->assertContains("00886", $crawler->filter("table")->html());
        $this->assertContains("Large", $crawler->filter("table")->html());
    }

    /**
     * Story 40a
     * Tests that you can add a Truck to the system with form and view in the list
     */
    public function testAddAndViewTruck()
    {
        //get the client
        $client = static::createClient(array(), array('PHP_AUTH_USER' => 'admin', 'PHP_AUTH_PW'   => 'password'));
        $client->followRedirects(true);
        //go to truck utility page
        $crawler = $client->request('GET', '/trucks');

        //use form to add truck
        $form = $crawler->selectButton('Add')->form();
        $form["appbundle_truck[truckId]"] = $this->truck->getTruckId();
        $form["appbundle_truck[type]"] = $this->truck->getType();
        $crawler = $client->submit($form);

        //check that the table contains the new truck
        $this->assertContains($this->truck->getTruckId(), $crawler->filter("table")->html());
        $this->assertContains($this->truck->getType(), $crawler->filter("table")->html());
    }

    //FINISHED RIGHT HERE -AB


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

        $crawler = $client->request('GET', '/route/1');

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

        $crawler = $client->request('GET', '/route/1');

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

        $crawler = $client->request('GET', '/route/1');

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

        //Make make sure the table does not contain the removed container
        $this->assertNotContains("X11111", $crawler->filter("table")->html());
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