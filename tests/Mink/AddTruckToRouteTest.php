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
     * This test checks the functionality of adding a truck to a route that does not already have a truck assigned to it,
     * But this time using the advanced search.
     */
    public function testAddTruckToRouteWithAdvancedSearch()
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
        // Get the names of all the windows
        $windowNames = $this->session->getWindowNames();
        // Take note of this current window so we can return to it later
        $originalWindow = $this->session->getWindowName();

        // Click on the advanced search button
        $page->find('named', array('button', 'Advanced Search'))->click();

        // WAIT for the page to load, otherwise it will be empty when mink tries to use it.
        $this->session->wait(2000);

        // Get the names of all the windows AGAIN, so we can figure out which one is new
        $newWindowNames = $this->session->getWindowNames();
        // Figure out which is new. The only new window should be at position 0 in the resulting array
        $popupWindowArray = array_diff($newWindowNames, $windowNames);

        // Switch to the first window in the array (seems to always be the popup)
        $this->session->switchToWindow($popupWindowArray[0]);

        // Re-get the page, since we are in a new window
        $page = $this->session->getPage();

        // Search for a truck
        $page->find('named', array('id', "searchBox"))->setValue("000034");

        // Emulate a keyup to trigger the event that normally does a search.
        // Don't know why, it doesn't matter what character you press as it doesn't seem to go in the box anyways.
        $page->find('named', array('id', "searchBox"))->keyPress("s");

        // Make Mink wait for the search to complete. This has to be REALLY long because the dev server is slow.
        $this->session->wait(5000);

        // click the first row for one of the results
        $selectLink = $page->find('css', 'table tbody tr:first-child');
        // Before we click the link, take the id of the truck we clicked.
        $id = $selectLink->getAttribute("data-id");
        //Click the link
        $selectLink->click();

        // Switch back to the original window
        $this->session->switchToWindow($originalWindow);

        // Refresh the page content so it reflects the window
        $page = $this->session->getPage();

        // Assert the select box has the chosen truck in it
        $this->assertEquals($page->find("css", "#truckDropdown")->getValue(), $id);

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