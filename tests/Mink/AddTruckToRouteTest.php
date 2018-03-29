<?php

require_once 'vendor/autoload.php';
use DMore\ChromeDriver\ChromeDriver;
use Behat\Mink\Session;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use AppBundle\DataFixtures\ORM\LoadUserData;
use AppBundle\DataFixtures\ORM\LoadContainerData;
use AppBundle\DataFixtures\ORM\LoadPropertyData;
use Tests\AppBundle\DatabasePrimer;

/**
 * Story 40b, associating trucks with routes
 */
class AddTruckToRouteTest extends WebTestCase
{
    private $driver;
    private $session;
    private $em;

    public static function setUpBeforeClass()
    {
        self::bootKernel();
        DatabasePrimer::prime(self::$kernel);
    }

    protected function setUp()
    {
        // Load the user fixture so you can actually log in
        self::bootKernel();
        $this->em = static::$kernel->getContainer()
            ->get('doctrine')
            ->getManager();

        // Also load in the containers so there is something to search for
        $containerLoader = new LoadContainerData();
        $containerLoader->load($this->em);

        $encoder = static::$kernel->getContainer()->get('security.password_encoder');

        $userLoader = new LoadUserData($encoder);
        $userLoader->load($this->em);

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
        $page->find('named', array('id_or_name', "login"))->submit();
        // Wait for the page to load before trying to browse elsewhere
        $this->session->wait(10000, "document.readyState === 'complete'");
    }


    /**
     * Story 40b
     * This test checks the functionality of adding a truck to a route that does not already have a truck assigned to it.
     */
    public function testAddTruckToRoute()
    {
        // Browse to the desired page
        $this->session->visit('http://localhost:8000/app_test.php/route/1');
        // Get the page
        $page = $this->session->getPage();

        // There should be no truck currently assigned, check for that
        $this->assertContains($page->find('css', 'h1.ui.header.left.attached.top')->getHtml(), "No Truck Assigned");
        // Assert that the assign truck form exists
        $this->assertTrue($page->find('css', '#assignTruckForm')->isVisible());
        // Make sure the change truck button is NOT visible
        $this->assertFalse($page->find('named', array('button', 'Change Truck'))->isVisible());
        // Make sure the unassign truck button is NOT visible
        $this->assertFalse($page->find('named', array('button', 'Unassign Truck'))->isVisible());

        // Fill out the truck form
        $page->find("css", "#truckDropdown")->setValue("000034");
        // Submit the truck form
        $page->find('named', array('button', 'Assign'))->click();

        // When the page reloads, assert that the truck is now assigned
        $this->assertContains($page->find('css', 'h1.ui.header.left.attached.top')->getHtml(), "Truck 000034");
        // Make sure the change truck button is visible
        $this->assertTrue($page->find('named', array('button', 'Change Truck'))->isVisible());
        // Make sure the unassign truck button is visible
        $this->assertTrue($page->find('named', array('button', 'Unassign Truck'))->isVisible());
        // Make sure the assign truck form is NOT visible
        $this->assertFalse($page->find('css', '#assignTruckForm')->isVisible());
    }

    /**
     * Story 40b
     * This test checks the functionality of changing a truck on a route that already has a truck assigned.
     */
    public function testChangeTruckOnRoute()
    {
        // Browse to the desired page
        $this->session->visit('http://localhost:8000/app_test.php/route/2');
        // Get the page
        $page = $this->session->getPage();

        // There should already be a truck assigned, check for that
        $this->assertContains($page->find('css', 'h1.ui.header.left.attached.top')->getHtml(), "Truck 000033");
        // Make sure the change truck button is visible
        $this->assertTrue($page->find('named', array('button', 'Change Truck'))->isVisible());
        // Make sure the unassign truck button is visible
        $this->assertTrue($page->find('named', array('button', 'Unassign Truck'))->isVisible());
        // Make sure the assign truck form is NOT visible
        $this->assertFalse($page->find('css', '#assignTruckForm')->isVisible());

        // Click the change truck button to open the form
        $page->find('named', array('button', 'Change Truck'))->click();

        // Fill out the truck form
        $page->find("css", "#truckDropdown")->setValue("000034");
        // Submit the truck form
        $page->find('named', array('button', 'Assign'))->click();

        // When the page reloads, assert that the truck is now assigned
        $this->assertContains($page->find('css', 'h1.ui.header.left.attached.top')->getHtml(), "Truck 000034");
        // Make sure the change truck button is visible
        $this->assertTrue($page->find('named', array('button', 'Change Truck'))->isVisible());
        // Make sure the unassign truck button is visible
        $this->assertTrue($page->find('named', array('button', 'Unassign Truck'))->isVisible());
        // Make sure the assign truck form is NOT visible
        $this->assertFalse($page->find('css', '#assignTruckForm')->isVisible());
    }

    /**
     * Story 40b
     * This test checks the functionality of removing a truck on a route that already has a truck assigned.
     */
    public function testRemoveTruckFromRoute()
    {
        // Browse to the desired page
        $this->session->visit('http://localhost:8000/app_test.php/route/2');
        // Get the page
        $page = $this->session->getPage();

        // There should already be a truck assigned, check for that
        $this->assertContains($page->find('css', 'h1.ui.header.left.attached.top')->getHtml(), "Truck 000033");
        // Make sure the change truck button is visible
        $this->assertTrue($page->find('named', array('button', 'Change Truck'))->isVisible());
        // Make sure the unassign truck button is visible
        $this->assertTrue($page->find('named', array('button', 'Unassign Truck'))->isVisible());
        // Make sure the assign truck form is NOT visible
        $this->assertFalse($page->find('css', '#assignTruckForm')->isVisible());

        // Click the remove truck button to open the form
        $page->find('named', array('button', 'Unassign Truck'))->click();

        // Assert that the modal is now visible
        $this->assertTrue($page->find('css', '#deleteModal')->isVisible());
        // Check the message on the modal
        $this->assertContains($page->find('css', '#deleteModal')->getHtml(), "Are you sure you want to unassign the truck 000033 from route 1002");
        // Click the delete button
        $page->find('css', '#deleteButton')->click();

        // When the page reloads, assert that there is no longer a truck assigned
        $this->assertContains($page->find('css', 'h1.ui.header.left.attached.top')->getHtml(), "No Truck Assigned");
        // Assert that the assign truck form exists
        $this->assertTrue($page->find('css', '#assignTruckForm')->isVisible());
        // Make sure the change truck button is NOT visible
        $this->assertFalse($page->find('named', array('button', 'Change Truck'))->isVisible());
        // Make sure the unassign truck button is NOT visible
        $this->assertFalse($page->find('named', array('button', 'Unassign Truck'))->isVisible());

    }

    protected function tearDown()
    {
        // After the test has been run, make sure to stop the session so you don't run into problems
        $this->session->stop();

        // Delete the user from the database
        $stmt = $this->em->getConnection()->prepare("DELETE FROM User");
        $stmt->execute();

        $this->em->close();
        $this->em = null; //avoid memory meaks
    }
}