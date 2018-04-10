<?php


use DMore\ChromeDriver\ChromeDriver;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Tests\AppBundle\DatabasePrimer;
use Behat\Mink\Session;

use AppBundle\DataFixtures\ORM\LoadUserData;
use AppBundle\DataFixtures\ORM\LoadCommunicationData;
use AppBundle\DataFixtures\ORM\LoadContactData;
use AppBundle\DataFixtures\ORM\LoadContainerData;
use AppBundle\DataFixtures\ORM\LoadPropertyData;
use AppBundle\DataFixtures\ORM\LoadRouteData;
use AppBundle\DataFixtures\ORM\LoadTruckData;
/**
 * NavigateSystemTest short summary.
 *
 * NavigateSystemTest description.
 *
 * @version 1.0
 * @author cst244
 */
class NavigateSystemTest extends WebTestCase
{
    private $driver;
    private $session;
    private $em;

    public static function setUpBeforeClass()
    {
        self::bootKernel();
        DatabasePrimer::prime(self::$kernel);

        $encoder = static::$kernel->getContainer()->get('security.password_encoder');

        //Load ALL fixtures (so that there is stuff to browse to)
        $userLoader = new LoadUserData($encoder);
        $userLoader->load(DatabasePrimer::$entityManager);

        $communicationLoader = new LoadCommunicationData();
        $communicationLoader->load(DatabasePrimer::$entityManager);

        $contactLoader = new LoadContactData();
        $contactLoader->load(DatabasePrimer::$entityManager);

        $containerLoader = new LoadContainerData();
        $containerLoader->load(DatabasePrimer::$entityManager);

        $propertyLoader = new LoadPropertyData();
        $propertyLoader->load(DatabasePrimer::$entityManager);

        $routeLoader = new LoadRouteData();
        $routeLoader->load(DatabasePrimer::$entityManager);

        $truckLoader = new LoadTruckData();
        $truckLoader->load(DatabasePrimer::$entityManager);
    }

    protected function setUp()
    {
        // get the entity manager
        self::bootKernel();
        $this->em = static::$kernel->getContainer()
            ->get('doctrine')
            ->getManager();

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

    protected function tearDown()
    {
        // After the test has been run, make sure to stop the session so you don't run into problems
        $this->session->stop();
    }

    // TEST HAMBURGER BUTTON VISIBILITY
    /**
        story 23a
        Tests the visibility of the menu bar, and the columns of the homepage links
    */
    public function testMenu()
    {
        //start up a new session, starting at the home page
        $this->session->visit('http://localhost:8000/app_test.php');
        // Get the page
        $page = $this->session->getPage();

        $menuButton = $page->find("css", "#openMenu");

        // Test that the hamburger button doesn't show up on desktop
        $this->assertFalse($menuButton->isVisible());

        //Tests that the links on the homepage are in columns of 2
        //loop for all 3 rows
        for ($i = 1; $i <= 3; $i+= 2)
        {
            //index of the second column
            $j = $i + 1;

            //checks that the items in each column have the same y index
        	$isTwoColumns = $this->session->evaluateScript("
                $('#mainLinks .column:nth-of-type($i)').position().top == $('#mainLinks .column:nth-of-type($j)').position().top
            ");

            //was the script true?
            //The script returns a string technically
            $this->assertTrue($isTwoColumns);
        }


        // Resize the window to mobile view
        $this->session->resizeWindow(766, 1080, "current");

        //Test that the next item in the list is not on the same y index
        //loop for the first 5 rows (6th row doesn't have a next row)
        for ($i = 1; $i <= 5; $i++)
        {
            //index of the next column
            $j = $i + 1;

            //checks that the items in each column do not have the same y index
        	$isOneColumn = $this->session->evaluateScript("
                $('#mainLinks .column:nth-of-type($i)').position().top == $('#mainLinks .column:nth-of-type($j)').position().top
            ");

            //Was the script false?
            $this->assertFalse($isOneColumn);
        }


        // Test that the hamburger button does show up on mobile
        $this->assertTrue($menuButton->isVisible());

        $menuButton->click();
        $this->session->wait(500);
        // Test that the sidebar pops out when the hamburger is pressed
        $this->assertTrue($page->find("css", "#sideNav")->isVisible());
    }

    //NAVIGATION TESTS BELOW
    /**
        story 23a
        Tests that you can browse to the homepage and everything shows up correctly
    */
    public function testBrowseHome()
    {
        //start up a new session, starting at the property search page
        $this->session->visit('http://localhost:8000/app_test.php/property/search');
        // Get the page
        $page = $this->session->getPage();

        //click on the home button in the nav menu
        $page->find("css","#homePage")->click();


        // Assert that we were redirected to the main page
        $this->assertRegExp('/\//', $this->session->getCurrentUrl());
        //check the homepage navigation links (in the page's body, not the navbar)
        $this->assertContains("Communications", $page->find("css","#homeCommunications")->getHtml());
        $this->assertContains("Contacts", $page->find("css","#homeContacts")->getHtml());
        $this->assertContains("Containers", $page->find("css","#homeContainers")->getHtml());
        $this->assertContains("Properties", $page->find("css","#homeProperties")->getHtml());
        $this->assertContains("Routes", $page->find("css","#homeRoutes")->getHtml());
        $this->assertContains("Trucks", $page->find("css","#homeTrucks")->getHtml());
    }

    /**
     * Story 23a
     * Tests that you can browse to the various communications pages (minus view, tested in search frontend)
     */
    public function testBrowseCommunications()
    {
        //start up a new session, starting at the home page
        $this->session->visit('http://localhost:8000/app_test.php');
        // Get the page
        $page = $this->session->getPage();

        //click on the communications button
        $page->find("css","#communicationsPage")->click();

        //check that the header is the communication search page
        $this->assertContains("Communication Search", $page->find("css","#contentSeparator h2")->getHtml());
        $this->assertContains("/communication/search", $this->session->getCurrentUrl());

        //Click on the add communication button
        $page->find("css","#newCommunication")->click();

        //check that the header is there and we're on the new communication page
        $this->assertContains("Add Communication" , $page->find("css","#contentSeparator h2")->getHtml());
        $this->assertContains("/communication/new", $this->session->getCurrentUrl());


        //check that the buttons on the new communication page are in the same y index
        $this->assertTrue($this->session->evaluateScript("
                $('.ui.buttons .ui.button:contains(Cancel)').position().top == $('.ui.buttons .ui.button:contains(Add)').position().top"
        ));


        //Attempt to click on the cancel button
        $page->find("css","#cancelButton")->click();

        //check that we're back on the search page
        $this->assertContains("Communication Search", $page->find("css","#contentSeparator h2")->getHtml());
        $this->assertContains("/communication/search", $this->session->getCurrentUrl());
    }

    /**
     * Story 23a
     * Tests that you can browse to the communication search page from the home page
     */
    public function testBrowseCommunicationsHome()
    {
        //start up a new session, starting at the home page
        $this->session->visit('http://localhost:8000/app_test.php');
        // Get the page
        $page = $this->session->getPage();

        //click on the communications button
        $page->find("css","#homeCommunications")->click();

        //check that the header is the communication search page
        $this->assertContains("Communication Search", $page->find("css","#contentSeparator h2")->getHtml());
        $this->assertContains("/communication/search", $this->session->getCurrentUrl());
    }

    /**
     * Story 23a
     * Tests that when you are on a communication view page that you can go back to the communication search page
     */
    public function testCommunicationsBack()
    {
        //start up a new session, go to the communication view page
        $this->session->visit('http://localhost:8000/app_test.php/communication/1');
        // Get the page
        $page = $this->session->getPage();

        //check that the buttons on the view communication page are in the same y index
        $this->assertTrue($this->session->evaluateScript('
                $(".ui.buttons .ui.button:contains(Back)").position().top == $(".ui.buttons .ui.button:contains(Edit)").position().top
        '));

        //click on the back button
        $page->find("css","#backButton")->click();

        //check that we're back on the search page
        $this->assertContains("Communication Search", $page->find("css","#contentSeparator h2")->getHtml());
        $this->assertContains("/communication/search", $this->session->getCurrentUrl());
    }

    /**
     * Story 23a
     * Tests that you can browse to the contact search and add pages (but not view, because that is tested in the frontend search)
     */
    public function testBrowseContacts()
    {
        //start up a new session, starting at the home page
        $this->session->visit('http://localhost:8000/app_test.php');
        // Get the page
        $page = $this->session->getPage();

        //click on the contacts button
        $page->find("css","#contactsPage")->click();

        //check that the header is the contact search page
        $this->assertContains("Contact Search", $page->find("css","h2")->getHtml());
        $this->assertContains("/contact/search", $this->session->getCurrentUrl());

        //Click on the add contact button
        $page->find("css","#newContact")->click();

        //check that the header is there and we're on the new contact page
        $this->assertContains("Add Contact" , $page->find("css","#contentSeparator h2")->getHtml());
        $this->assertContains("/contact/new", $this->session->getCurrentUrl());


        //check that the buttons on the new contact page are in the same y index
        $this->assertTrue( $this->session->evaluateScript('
                $(".ui.buttons .ui.button:contains(Cancel)").position().top == $(".ui.buttons .ui.button[value=\"Add\"").position().top
        '));

        //Attempt to click on the cancel button
        $page->find("css","#cancelButton")->click();

        //check that we're back on the search page
        $this->assertContains("Contact Search", $page->find("css","#contentSeparator h2")->getHtml());
        $this->assertContains("/contact/search", $this->session->getCurrentUrl());
    }

    /**
     * Story 23a
     * Tests that you can browse to the contact search page from the home page
     */
    public function testBrowseContactsHome()
    {
        //start up a new session, starting at the home page
        $this->session->visit('http://localhost:8000/app_test.php');
        // Get the page
        $page = $this->session->getPage();

        //click on the contact button
        $page->find("css","#homeContacts")->click();

        //check that the header is the contact search page
        $this->assertContains("Contact Search", $page->find("css","#contentSeparator h2")->getHtml());
        $this->assertContains("/contact/search", $this->session->getCurrentUrl());
    }

    /**
     * Story 23a
     * Tests that when you are on a contact view page that you can go back to the contact search page
     */
    public function testContactsBack()
    {
        //start up a new session, go to the contact view page
        $this->session->visit('http://localhost:8000/app_test.php/contact/1');
        // Get the page
        $page = $this->session->getPage();


        //check that the buttons on the view contact page are in the same y index
        $this->assertTrue( $this->session->evaluateScript('
                cancelButtonY = $(".ui.buttons .ui.button:contains(Back)").position().top;
                cancelButtonY == $(".ui.buttons .ui.button:contains(Edit)").position().top && cancelButtonY == $(".ui.buttons .ui.button:contains(Delete)").position().top;
        '));


        //click on the back button
        $page->find("css","#backButton")->click();

        //check that we're back on the search page
        $this->assertContains("Contact Search", $page->find("css","#contentSeparator h2")->getHtml());
        $this->assertContains("/contact/search", $this->session->getCurrentUrl());
    }

    /**
     * Story 23a
     * Tests that you can browse the container search and add pages (but not view, it is tested in the container search story)
     */
    public function testBrowseContainers()
    {
        //start up a new session, starting at the home page
        $this->session->visit('http://localhost:8000/app_test.php');
        // Get the page
        $page = $this->session->getPage();

        //click on the containers button
        $page->find("css","#containersPage")->click();

        //check that the header is the container search page
        $this->assertContains("Container Search", $page->find("css","#contentSeparator h2")->getHtml());
        $this->assertContains("/container/search", $this->session->getCurrentUrl());

        //Click on the add container button
        $page->find("css","#newContainer")->click();

        //check that the header is there and we're on the new container page
        $this->assertContains("Add Container" , $page->find("css","#contentSeparator h2")->getHtml());
        $this->assertContains("/container/new", $this->session->getCurrentUrl());

        //check that the buttons on the new container page are in the same y index
        $this->assertTrue($this->session->evaluateScript('
                $(".ui.buttons .ui.button:contains(Cancel)").position().top == $(".ui.buttons .ui.button[value=\"Add\"").position().top
        '));

        //Attempt to click on the cancel button
        $page->find("css","#cancelButton")->click();

        //check that we're back on the search page
        $this->assertContains("Container Search", $page->find("css","#contentSeparator h2")->getHtml());
        $this->assertContains("/container/search", $this->session->getCurrentUrl());
    }

    /**
     * Story 23a
     * Tests that you can browse to the container search page from the home page
     */
    public function testBrowseContainersHome()
    {
        //start up a new session, starting at the home page
        $this->session->visit('http://localhost:8000/app_test.php');
        // Get the page
        $page = $this->session->getPage();

        $this->session->wait(100);

        //click on the container button
        $page->find("css","#homeContainers")->click();

        //check that the header is the container search page
        $this->assertContains("Container Search", $page->find("css","#contentSeparator h2")->getHtml());
        $this->assertContains("/container/search", $this->session->getCurrentUrl());
    }

    /**
     * Story 23a
     * Tests that when you're on the container view page you can get back to the container search page
     */
    public function testContainersBack()
    {
        //start up a new session, go to the container view page
        $this->session->visit('http://localhost:8000/app_test.php/container/1');
        // Get the page
        $page = $this->session->getPage();

        //check that the buttons on the view container page are in the same y index
        $this->assertTrue( $this->session->evaluateScript('
                cancelButtonY = $(".ui.buttons .ui.button:contains(Back)").position().top;
                cancelButtonY == $(".ui.buttons .ui.button:contains(Edit)").position().top && cancelButtonY == $(".ui.buttons .ui.button:contains(Delete)").position().top;
        '));

        //click on the back button
        $page->find("css","#backButton")->click();


        //check that we're back on the search page
        $this->assertContains("Container Search", $page->find("css","#contentSeparator h2")->getHtml());
        $this->assertContains("/container/search", $this->session->getCurrentUrl());
    }

    /**
     * Story 23a
     * Tests that you can browse to the property search and add pages (Not view, tested in the frontend search)
     */
    public function testBrowseProperties()
    {
        //start up a new session, starting at the home page
        $this->session->visit('http://localhost:8000/app_test.php');
        // Get the page
        $page = $this->session->getPage();

        //click on the containers button
        $page->find("css","#propertiesPage")->click();

        //check that the header is the property search page
        $this->assertContains("Property Search", $page->find("css","#contentSeparator h2")->getHtml());
        $this->assertContains("/property/search", $this->session->getCurrentUrl());

        //Click on the add property button
        $page->find("css","#newProperty")->click();

        //check that the header is there and we're on the new property page
        $this->assertContains("Add Property" , $page->find("css","#contentSeparator h2")->getHtml());
        $this->assertContains("/property/new", $this->session->getCurrentUrl());

        //check that the buttons on the new property page are in the same y index
        $this->assertTrue( $this->session->evaluateScript('
                $(".ui.buttons .ui.button:contains(Cancel)").position().top == $(".ui.buttons .ui.button[value=\"Add\"").position().top
        '));


        //Attempt to click on the cancel button
        $page->find("css","#cancelButton")->click();

        //check that we're back on the search page
        $this->assertContains("Property Search", $page->find("css","#contentSeparator h2")->getHtml());
        $this->assertContains("/property/search", $this->session->getCurrentUrl());
    }

    /**
     * Story 23a
     * Tests that you can browse to the property search page from the home page
     */
    public function testBrowsePropertiesHome()
    {
        //start up a new session, starting at the home page
        $this->session->visit('http://localhost:8000/app_test.php');
        // Get the page
        $page = $this->session->getPage();

        //click on the property button
        $page->find("css","#homeProperties")->click();

        //check that the header is the property search page
        $this->assertContains("Property Search", $page->find("css","#contentSeparator h2")->getHtml());
        $this->assertContains("/property/search", $this->session->getCurrentUrl());
    }

    /**
     * Story 23a
     * Tests that when you're on the container view page you can get back to the container search page
     */
    public function testPropertiesBack()
    {
        //start up a new session, go to the property view page
        $this->session->visit('http://localhost:8000/app_test.php/property/1');
        // Get the page
        $page = $this->session->getPage();

        //check that the buttons on the view property page are in the same y index
        $this->assertTrue( $this->session->evaluateScript('
                $(".ui.buttons .ui.button:contains(Back)").position().top == $(".ui.buttons .ui.button:contains(Edit)").position().top
        '));

        //click on the back button
        $page->find("css","#backButton")->click();

        //check that we're back on the search page
        $this->assertContains("Property Search", $page->find("css","#contentSeparator h2")->getHtml());
        $this->assertContains("/property/search", $this->session->getCurrentUrl());
    }

    /**
     * Story 23a
     * Tests that you can browse to the route index page
     */
    public function testBrowseRoutes()
    {
        //start up a new session, starting at the home page
        $this->session->visit('http://localhost:8000/app_test.php');
        // Get the page
        $page = $this->session->getPage();

        //click on the routes button
        $page->find("css","#routesPage")->click();

        //check that the header is the route index page
        $this->assertContains("Route Search", $page->getHtml());
        $this->assertContains("/route/search", $this->session->getCurrentUrl());
    }

    /** MOVED TO MINK
     * Story 23a
     * Tests that you can browse to the route index page from the home page
     */
    /*public function testBrowseRoutesManage()
    {
        //start up a new session, starting at the home page
        $this->session->visit('http://localhost:8000/app_test.php');
        // Get the page
        $page = $this->session->getPage();

        //click on the routes button
        $page->find("css","#routesPage")->click();
        $page->find("css","#routesPage")->click();

        //check that the header is the route index page
        $this->assertContains("Add Route", $page->find("css","#contentSeparator h2")->getHtml());
        $this->assertContains("/route", $this->session->getCurrentUrl());
    }*/

    /**
     * Story 23a
     * Tests that you can browse to the truck index page (edit and back is already tested)
     */
    public function testBrowseTrucks()
    {
        //start up a new session, starting at the home page
        $this->session->visit('http://localhost:8000/app_test.php');
        // Get the page
        $page = $this->session->getPage();

        //click on the trucks button
        $page->find("css","#trucksPage")->click();

        //check that the header is the truck index page
        $this->assertContains("Add Truck", $page->find("css","#contentSeparator h2")->getHtml());
        $this->assertContains("/truck", $this->session->getCurrentUrl());
    }

    /**
     * Story 23a
     * Tests that you can browse to the truck index page from the home page
     */
    public function testBrowseTrucksHome()
    {
        //start up a new session, starting at the home page
        $this->session->visit('http://localhost:8000/app_test.php');
        // Get the page
        $page = $this->session->getPage();

        //click on the truck button
        $page->find("css","#homeTrucks")->click();

        //check that the header is the truck index page
        $this->assertContains("Add Truck", $page->find("css","#contentSeparator h2")->getHtml());
        $this->assertContains("/truck", $this->session->getCurrentUrl());
    }
}