<?php
namespace Tests\Mink;
require_once 'vendor/autoload.php';
use DMore\ChromeDriver\ChromeDriver;
use Behat\Mink\Session;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use AppBundle\DataFixtures\ORM\LoadRouteData;
use AppBundle\DataFixtures\ORM\LoadUserData;
use AppBundle\Entity\Truck;
use Tests\AppBundle\DatabasePrimer;

/**
    This class tests functionality of the truck utility page.
*/
class RouteUtilPageTest extends WebTestCase
{
    private $driver;
    private $session;
    private $em;

    public static function setUpBeforeClass()
    {
        self::bootKernel();
        DatabasePrimer::prime(self::$kernel);

        $encoder = static::$kernel->getContainer()->get('security.password_encoder');

        $userLoader = new LoadUserData($encoder);
        $userLoader->load(DatabasePrimer::$entityManager);
    }

    protected function setUp()
    {
        self::bootKernel();

        $this->em = static::$kernel->getContainer()
            ->get('doctrine')
            ->getManager();

        // Load a truck into the DB
        $encoder = static::$kernel->getContainer()->get('security.password_encoder');

        //$userLoader = new LoadUserData($encoder);
        //$userLoader->load($this->em);

        // Create a driver
        $this->driver = new ChromeDriver("http://localhost:9222",null, "localhost:8000");
        // Create a session and pass it the driver
        $this->session = new Session($this->driver);

        //Log the user in
        // Start the session
        $this->session->start();

        // go to the login page
        $this->session->visit('http://localhost:8000/app_test.php/login');
        // Get the current page
        $page = $this->session->getPage();
        // Fill out the login form
        $page->findById("username")->setValue("admin");
        $page->findById("password")->setValue("password");
        // Submit the form
        $page->pressButton('Log In');//replaced findButton('Log In')->click();
        // Wait for the page to load before trying to browse elsewhere
        $this->session->wait(10000, "document.readyState === 'complete'");


        // Load in trucks from our truck fixture
        $routeLoader = new LoadRouteData();
        $routeLoader->load($this->em);
    }


    // A BUNCH of these tests are deprecated because our story was changed to be something totally different.
    /**
     * Story 40c
     * Tests that the user can create a new route successfully with the add route form with auto populated values
     */
    /*
    public function testAddNewRouteSuccessAutoValues()
    {
        // Navigate to the route add page
        $this->session->visit('http://localhost:8000/app_test.php/route');
        // Get the page
        $page = $this->session->getPage();

        // Assert that the form exists on the page
        $this->assertNotNull($page->find('css', 'appbundle_route'));

        // The route ID should automatically be populated with the next ID from the database
        $this->assertEquals($page->find('named', array('field', 'Route ID'))->getValue(), "1001");
        // The date should be today's current date
        $todayDate = date("MM/DD/YYYY");
        $this->assertEquals($page->find('named', array('field', 'Start Date'))->getValue(), $todayDate);

        // Try submitting with these default values
        $page->find('css', 'appbundle_route')->submit();

        // Assert we are on the page for the route
        $this->assertContains('/route/3', $this->session->getCurrentUrl());

        // When the page reloads on the new route, assert that it has the new route ID and the start time
        $this->assertContains($page->find("css", "h1")->getHtml(), "Route 1001");
        $this->assertContains($page->find("css", "h1")->getHtml(), "Started: " . $todayDate);

    }
    */

    /**
     * Story 40c
     * Submit the new route form wrong and assert the errors on the page
     */
    /*
    public function testInvalidNewRoute()
    {
        // Navigate to the route add page
        $this->session->visit('http://localhost:8000/app_test.php/route');
        // Get the page
        $page = $this->session->getPage();

        // Assert that the form exists on the page
        $this->assertNotNull($page->find('css', 'appbundle_route'));

        // Fill out the form INCORRECTLY
        $page->find('named', array('field', 'Route ID'))->setValue("-444");
        $page->find('named', array('field', 'Start Date'))->setValue("01/01/1970");


        // Try submitting the form
        $page->find('css', 'appbundle_route')->submit();

        // Assert we are still on the same page
        $this->assertContains('/route', $this->session->getCurrentUrl());

        // Assert that the errors are displayed on the form
        $this->assertContains($page->find("css", "ui.message")->getHtml(), "Route must be a positive integer");
        $this->assertContains($page->find("css", "ui.message")->getHtml(), "Start date cannot be in the past");

        // Try again but with an existing route ID
        $page->find('named', array('field', 'Route ID'))->setValue("1001");
        $page->find('css', 'appbundle_route')->submit();
        $this->assertContains($page->find("css", "ui.message")->getHtml(), "Route '1001' already exists in the system");
    }*/

    /**
     * Story 40c
     * Tests that the user can create a new route successfully with the add route form manually specifying values
     */
    /*
    public function testAddNewRouteSuccessManual()
    {
        // Navigate to the route add page
        $this->session->visit('http://localhost:8000/app_test.php/route');
        // Get the page
        $page = $this->session->getPage();

        // Assert that the form exists on the page
        $this->assertNotNull($page->find('css', 'appbundle_route'));

        // Fill out the form correctly
        $page->find('named', array('field', 'Route ID'))->setValue("1005");
        $page->find('named', array('field', 'Start Date'))->setValue("01/01/2033");

        // Try submitting with these default values
        $page->find('css', 'appbundle_route')->submit();

        // Assert we are on the page for the new route
        $this->assertContains('/route/3', $this->session->getCurrentUrl());

        // When the page reloads on the new route, assert that it has the new route ID and the start time
        $this->assertContains($page->find("css", "h1")->getHtml(), "Route 1005");
        $this->assertContains($page->find("css", "h1")->getHtml(), "Started: 01/01/2033");

    }
    */

    /**
     * Story 40c
     * Tests successfully creating a new route template
     * THROWING EM ERROR FOR NO REASON
     */
    public function testCreateNewRouteTemplate()
    {
        // Navigate to the route add page
        $this->session->visit('http://localhost:8000/app_test.php/route/search');
        // Get the page
        $page = $this->session->getPage();

        // click the new route template button
        $page->find('css', '#newRouteOptions')->click();
        $this->session->wait(500);

        $page->find('css', "#newRouteTemplate")->click();
        $this->session->wait(1000);

        // Assert we are now on the new route template page
        $this->assertContains('/route/template/new', $this->session->getCurrentUrl());

        // Fill out the template name
        $page->find('named', array('field', 'Template Name:'))->setValue("Sample Template");

        // Now add the template to the system
        $page->find('named', array('button', 'Add'))->click();
        $this->session->wait(6000);

        // Assert we are on the edit route page
        $this->assertContains('/route/manage/template', $this->session->getCurrentUrl());
    }

    /**
     * Story 40c
     * Checks what happens when you try to submit the route template form without actually filling out the page
     */
    public function testCreateNewTemplateCantAddInvalid()
    {
        //// Do the same as above test but don't fill out the form and then submit it. Check for the errors to appear
        // Navigate to the route add page
        // Navigate to the route add page
        $this->session->visit('http://localhost:8000/app_test.php/route/search');
        // Get the page
        $page = $this->session->getPage();

        //make sure modal isnt visible before clicking add options button
        $modal = $page->find("css", ".modal");
        $this->assertFalse($modal->isVisible());

        // click the new route template button
        $page->find('css', '#newRouteOptions')->click();
        $this->session->wait(500);

        //make sure modal is visible before clicking add options button
        $modal = $page->find("css", ".modal");
        $this->assertTrue($modal->isVisible());

        $page->find('css', "#newRouteTemplate")->click();
        $this->session->wait(1000);

        // Assert we are now on the new route template page
        $this->assertContains('/route/template/new', $this->session->getCurrentUrl());

        // Now try to add the template to the system
        $page->find('named', array('button', 'Add'))->click();
        $this->session->wait(6000);

        // Assert we are still on the add route page
        $this->assertContains('/route/template/new', $this->session->getCurrentUrl());
    }

    /**
     * Story 40c
     * Checks Creating a new route from a template
     */
    /* MANUALLY DEMO THIS
    public function testCreateNewRouteFromTemplate()
    {
        // Navigate to the route add page
        $this->session->visit('http://localhost:8000/app_test.php/route/search');
        // Get the page
        $page = $this->session->getPage();

        // click the new route button
        $page->find('css', '#newRouteOptions')->click();
        $this->session->wait(500);

        $page->find('css', "#newRoute")->click();
        $this->session->wait(1000);

        // Assert we are now on the new route page
        $this->assertContains('/route/new', $this->session->getCurrentUrl());

        //set route id
        $page->find('named', array('field', 'Route ID:'))->setValue("735701");
        //set route date
        $page->find('named', array('field', 'Start Date:'))->setValue("01/01/2024");
        //select template from semantic picker
        $page->findById('appbundle_route_template')->click();

        $addRouteBtn = $page->find('named', array('button', 'Add'));
        $this->assertNotNull($addRouteBtn);
        $addRouteBtn->click();
        $this->session->wait(1000);

        // Assert we are now on the edit route page
        $this->assertContains('/route/manage/', $this->session->getCurrentUrl());

        //make sure all the data was added to the new route
        $this->assertContains($page->find("css", "h1")->getHtml(), "735701");
        $this->assertNotNull($page->find("css", "#route_pickups")->find('named', array('content', "T3STSRL")));
        $this->assertNotNull($page->find("css", "#route_pickups")->find('named', array('content', "T3STSR2")));
    }*/

    /**
     * Story 40c
     * Checks Errors when you try to create a route from a template without filling anything out (AKA ERRORS)
     */
    public function testCreateNewRouteErrorsAppear()
    {
        // Navigate to the route add page
        $this->session->visit('http://localhost:8000/app_test.php/route/search');
        // Get the page
        $page = $this->session->getPage();

        // click the new route button
        $page->find('css', '#newRouteOptions')->click();
        $this->session->wait(500);
        $page->find('css', "#newRoute")->click();
        $this->session->wait(1000);

        // Assert we are now on the new route page
        $this->assertContains('/route/new', $this->session->getCurrentUrl());

        //set route id
        $page->find('named', array('field', 'Route ID:'))->setValue("1001");

        $addRouteBtn = $page->find('named', array('button', 'Add'));
        $this->assertNotNull($addRouteBtn);
        $addRouteBtn->click();
        $this->session->wait(1000);

        // Assert that the errors can be displayed on the form
        //Route ID
        $this->assertContains("Route ID Already exists",$page->find("css", ".ui.message")->getHtml());

        $this->session->wait(1000);
        $page = $this->session->getPage();
        //set route id
        $page->find('named', array('field', 'Route ID:'))->setValue("AYY");

        $addRouteBtn = $page->find('named', array('button', 'Add'));
        $this->assertNotNull($addRouteBtn);
        $addRouteBtn->click();
        $this->session->wait(1000);

        //TODO
        $this->assertContains("The Route ID must contain 1 to 6 digits, no letters",$page->find("css", ".ui.message")->getHtml());
    }


    /**
     * (@inheritDoc)
     */
    protected function tearDown()
    {
        parent::tearDown();
        // After the test has been run, make sure to restart the session so you don't run into problems
        $this->session->stop();

        //Now wipe the database
        $stmt = $this->em->getConnection()->prepare('DELETE FROM Route');
        $stmt->execute();
        $stmt = $this->em->getConnection()->prepare('DELETE FROM Container');
        $stmt->execute();
        $stmt =$this->em->getConnection()->prepare('DELETE FROM RoutePickup');
        $stmt->execute();
        $stmt = $this->em->getConnection()->prepare('DELETE FROM User');
        $stmt->execute();
    }

}