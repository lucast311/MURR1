<?php
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use AppBundle\Entity\Route;
use AppBundle\Entity\Container;
use AppBundle\Entity\RoutePickup;
use AppBundle\DataFixtures\ORM\LoadUserData;


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

        // Load the admin user into the database so they can log in
        $encoder = static::$kernel->getContainer()->get('security.password_encoder');

        $userLoader = new LoadUserData($encoder);
        $userLoader->load($this->em);
    }

    /**
     * Story 22b
     * Tests that you can add a container to the route and it shows up on the page
     */
    public function testAddRoute()
    {
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

        $crawler = $client->request('GET', '/route/1');

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
    public function testInsertRoute()
    {
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
    public function testInsertBeyond()
    {
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
    public function testInsertExisting()
    {
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
        $client = static::createClient(array(), array('PHP_AUTH_USER' => 'admin', 'PHP_AUTH_PW' => 'password'));

        $crawler = $client->request('GET', '/route/1');

        $form = $crawler->selectButton('Add')->form();
        $form["appbundle_routepickup[pickupOrder]"] = 1;
        $form["appbundle_routepickup[container]"] = 1; //select the first container in the list so that it is attempting to add it again

        $crawler = $client->submit($form);

        //Check that the required error message is on the page
        $this->assertContains("This container already exists in this route", $client->getResponse()->getContent());
    }


    /**
     * Story 22a
     * Tests that you can import a route from a csv and that the add another [route] button works
     */
    public function testImportRouteAndAddAnotherSuccess()
    {
        //get the client
        $client = static::createClient(array(), array('PHP_AUTH_USER' => 'admin', 'PHP_AUTH_PW' => 'password'));
        $client->followRedirects(true);

        $crawler = $client->request('GET', '/route/import');

        $form = $crawler->selectButton('Upload')->form();
        $form['routeID'] = 64920;
        // Upload a valid route CSV
        $form['routeCSV']->upload('./testData/routes/valid_route.csv');

        $crawler = $client->submit($form);

        //check that browser was redirected to the successfully added message page
        $this->assertContains("was successfully added!",$client->getResponse()->getContent());

        $addAnotherButton = $crawler
            ->filter('button:contains("Add Another")') // find all buttons with the text "Add"
            ->eq(0) // select the first button in the list
            ->link(); // and click it

        $client->click($addAnotherButton);

        // Assert that we were redirected back to the import page
        $this->assertContains("/route/import", $client->getHistory()->current()->getUri());
    }

        /**
     * Story 22a
     * Tests that you can import a route from a csv and that the add another [route] button works
     */
    public function testImportRouteAndViewDetails()
    {
        //get the client
        $client = static::createClient(array(), array('PHP_AUTH_USER' => 'admin', 'PHP_AUTH_PW' => 'password'));
        $client->followRedirects(true);

        $crawler = $client->request('GET', '/route/import');

        $form = $crawler->selectButton('Upload')->form();
        $form['routeID'] = 64920;
        // Upload a valid route CSV
        $form['routeCSV']->upload('./testData/routes/valid_route.csv');

        $crawler = $client->submit($form);

        //check that browser was redirected to the successfully added message page
        $this->assertContains("was successfully added!",$client->getResponse()->getContent());

        $viewDetailsButton = $crawler
            ->filter('button:contains("View Details")') // find all buttons with the text "View Details"
            ->eq(0) // select the first button in the list
            ->link(); // and click it

        $client->click($viewDetailsButton);

        // Assert that we were redirected back to the import page
        $this->assertContains("/route/1", $client->getHistory()->current()->getUri());
    }


    /**
     * Story 22a
     * Tests that you cant import a route larger than 1 mb
     */
    public function testRouteFailTooLargeCSV()
    {
        //get the client
        $client = static::createClient(array(), array('PHP_AUTH_USER' => 'admin', 'PHP_AUTH_PW' => 'password'));
        $client->followRedirects(true);

        $crawler = $client->request('GET', '/route/import');

        $form = $crawler->selectButton('Upload')->form();
        $form['routeID'] = 64920;
        // Upload an invalid route CSV
        $form['routeCSV']->upload('./testData/routes/invalid_route_too_large.csv');

        $crawler = $client->submit($form);

        //check that browser was redirected to the successfully added message page
        $this->assertContains("Route files must be less than 1 MB in size.", $client->getResponse()->getContent());
    }

    /**
     * Story 22a
     * Tests that you cant import a route with a routeID whats already in use
     */
    public function testRouteFailDuplicateIDs()
    {
        //get the client
        $client = static::createClient(array(), array('PHP_AUTH_USER' => 'admin', 'PHP_AUTH_PW' => 'password'));
        $client->followRedirects(true);

        $crawler = $client->request('GET', '/route/import');

        $form = $crawler->selectButton('Upload')->form();
        $form['routeID'] = 64920;
        // Upload a valid route CSV
        $form['routeCSV']->upload('./testData/routes/valid_route.csv');

        $crawler = $client->submit($form);

        //check that browser was redirected to the successfully added message page
        $this->assertContains("was successfully added!", $client->getResponse()->getContent());

        $addAnotherButton = $crawler
            ->filter('button:contains("Add Another")') // find all buttons with the text "Add"
            ->eq(0) // select the first button in the list
            ->link(); // and click it

        $client->click($addAnotherButton);

        $form = $crawler->selectButton('Upload')->form();
        $form['routeID'] = 64920;
        // Upload a valid route CSV with the same route ID
        $form['routeCSV']->upload('./testData/routes/valid_route2.csv');

        $crawler = $client->submit($form);

        // Assert that the route wasn't added and an error message popped up.
        $this->assertContains("The route '64920' is already in user, please specify an alternative route ID.",$client->getResponse()->getContent());
    }

    /**
     * Story 22a
     * Tests that a user cannot add a route without a CSV
     */
    public function testImportRouteFailNoCSV()
    {
        //get the client
        $client = static::createClient(array(), array('PHP_AUTH_USER' => 'admin', 'PHP_AUTH_PW' => 'password'));
        $client->followRedirects(true);

        $crawler = $client->request('GET', '/route/import');

        $form = $crawler->selectButton('Upload')->form();
        $form['routeID'] = 64920;
        // Do not upload a CSV

        // Submit the incomplete form
        $crawler = $client->submit($form);

        // Ensure that the form did not submit
        $this->assertContains("Please upload a valid route CSV", $client->getResponse()->getContent());
    }

     /**
     * Story 22a
     * Tests that a CSV cannot have duplicate serials
     */
    public function testImportRouteFailDuplicateSerials()
    {
        //get the client
        $client = static::createClient(array(), array('PHP_AUTH_USER' => 'admin', 'PHP_AUTH_PW' => 'password'));
        $client->followRedirects(true);

        $crawler = $client->request('GET', '/route/import');

        $form = $crawler->selectButton('Upload')->form();
        $form['routeID'] = 64920;
        // Upload a CSV with duplicate serials
        $form['routeCSV']->upload('./testData/routes/invalid_route_dup_serials.csv');

        // Submit the form
        $crawler = $client->submit($form);

        // Ensure that the form did not submit
        $this->assertContains("Invalid route file: contains duplicate bin serials.", $client->getResponse()->getContent());
    }

    /**
     * Story 22a
     * Tests that the user cannot submit a blank form
     */
    public function testImportRouteFailBlankForm()
    {
        //get the client
        $client = static::createClient(array(), array('PHP_AUTH_USER' => 'admin', 'PHP_AUTH_PW' => 'password'));
        $client->followRedirects(true);

        $crawler = $client->request('GET', '/route/import');

        // Add a blank form
        $form = $crawler->selectButton('Upload')->form();

        // Submit the form
        $crawler = $client->submit($form);

        // Ensure that the form did not submit
        $this->assertContains("Please specify a route ID.", $client->getResponse()->getContent());
    }

     /**
     * Story 22a
     * Tests that csv serial's must match containers in the system
     */
    public function testImportRouteFailSerialDoNotMatch()
    {
        // get the client
        $client = static::createClient(array(), array('PHP_AUTH_USER' => 'admin', 'PHP_AUTH_PW' => 'password'));
        $client->followRedirects(true);

        $crawler = $client->request('GET', '/route/import');

        // Add a blank form
        $form = $crawler->selectButton('Upload')->form();
        $form['routeID'] = 64920;
        // Upload a CSV with duplicate serials
        $form['routeCSV']->upload('./testData/routes/invalid_route_nonexistent_serials.csv');

        // Submit the form
        $crawler = $client->submit($form);

        // Ensure that the form did not submit
        $this->assertContains("The following containers are not currently being tracked:", $client->getResponse()->getContent());
    }

     /**
     * Story 22a
     * Tests that only valid CSVs can be uploaded
     */
    public function testImportRouteFailInvalidCSVFormat()
    {
        // get the client
        $client = static::createClient(array(), array('PHP_AUTH_USER' => 'admin', 'PHP_AUTH_PW' => 'password'));
        $client->followRedirects(true);

        $crawler = $client->request('GET', '/route/import');

        // Add a blank form
        $form = $crawler->selectButton('Upload')->form();
        $form['routeID'] = 64920;
        // Upload a CSV with duplicate serials
        $form['routeCSV']->upload('./testData/routes/invalid_route.jpeg.csv');

        // Submit the form
        $crawler = $client->submit($form);

        // Ensure that the form did not submit
        $this->assertContains("Please upload a valid route CSV.", $client->getResponse()->getContent());
    }

         /**
     * Story 22a
     * Tests that only CSVs can be uploaded
     */
    public function testImportRouteFailWrongFileType()
    {
        // get the client
        $client = static::createClient(array(), array('PHP_AUTH_USER' => 'admin', 'PHP_AUTH_PW' => 'password'));
        $client->followRedirects(true);

        $crawler = $client->request('GET', '/route/import');

        // Add a blank form
        $form = $crawler->selectButton('Upload')->form();
        $form['routeID'] = 64920;
        // Upload a CSV with duplicate serials
        $form['routeCSV']->upload('./testData/routes/invalid_route.jpeg');

        // Submit the form
        $crawler = $client->submit($form);

        // Ensure that the form did not submit
        $this->assertContains("Invalid route file type: expected '.csv', receieved '.jpeg.'", $client->getResponse()->getContent());
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