<?php
namespace Tests\Mink;
require_once 'vendor/autoload.php';
use DMore\ChromeDriver\ChromeDriver;
use Behat\Mink\Session;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use AppBundle\DataFixtures\ORM\LoadUserData;
use AppBundle\DataFixtures\ORM\LoadPropertyData;
use AppBundle\DataFixtures\ORM\LoadContactData;
use Tests\AppBundle\DatabasePrimer;

/**
 * This test uses mink for browser based front-end testing of the javascript used in story 4e
 */
class PropertySearchPopupTest extends WebTestCase
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
        self::bootKernel();

        $this->em = static::$kernel->getContainer()
            ->get('doctrine')
            ->getManager();

        // Also load in the properties so there is something to search for
        $propertyLoader = new LoadPropertyData();
        $propertyLoader->load($this->em);

        $contactLoader = new LoadContactData();
        $contactLoader->load($this->em);

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
     * Story 4e
     * Tests all functionality related to the advanced property search on the communication page.
     * Ensures the button is there, that it functions correctly, and that the chosen property is set in the search box.
     *
     * HEY! This test will not run if popups are not allowed in chrome. GO ALLOW POPUPS IN CHROME.
     */
    public function testCommunicationPropertyAdvancedSearch()
    {
        // Start up a new session
        //$this->session = new Session($this->driver);
        //$this->session->start();

        // Navigate to the new communication page
        $this->session->visit('http://localhost:8000/app_test.php/communication/new');
        // Get the page
        $page = $this->session->getPage();
        // find and assert that there is an advanced search button
        $advancedSearchBtn = $page->find('named', array('button', "Advanced Search"));
        $this->assertNotNull($advancedSearchBtn);
        $this->assertEquals($advancedSearchBtn->getValue(), "Advanced Search");

        // Get the names of all the windows
        $windowNames = $this->session->getWindowNames();
        // Take note of this current window so we can return to it later
        $originalWindow = $this->session->getWindowName();

        // Click on the advanced search button
        $advancedSearchBtn->click();

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

        // Search box
        $this->assertNotNull($page->find('named', array('id', "searchBox")));
        // Table headers
        $this->assertNotNull($page->find('named', array('content', "Site ID")));
        $this->assertNotNull($page->find('named', array('content', "Property")));
        $this->assertNotNull($page->find('named', array('content', "Type")));
        $this->assertNotNull($page->find('named', array('content', "Status")));
        $this->assertNotNull($page->find('named', array('content', "Structure Id")));
        $this->assertNotNull($page->find('named', array('content', "Units")));
        $this->assertNotNull($page->find('named', array('content', "Neighbourhood")));

        // Search for a property
        $page->find('named', array('id', "searchBox"))->setValue("Charlton Arms");

        // Emulate a keyup to trigger the event that normally does a search.
        // Don't know why, it doesn't matter what character you press as it doesn't seem to go in the box anyways.
        $page->find('named', array('id', "searchBox"))->keyPress("s");

        // Make Mink wait for the search to complete. This has to be REALLY long because the dev server is slow.
        $this->session->wait(5000);

        // click the first row for one of the results
        $selectLink = $page->find('named', array('content', "3593843"));
        // Before we click the link, take the id of the property we clicked.
        $id = $selectLink->getParent()->getAttribute("data-id");
        //Click the link
        $selectLink->click();

        // Switch back to the original window
        $this->session->switchToWindow($originalWindow);

        // Refresh the page content so it reflects the window
        $page = $this->session->getPage();

        // Get the select box now and check that it has the right property in it (based on the id of the property that was clicked originally)
        $this->assertEquals($page->find('named', array('id', "appbundle_communication_property"))->getValue(), $id);
    }

    /**
     * Story 4e
     * This will test the functionality of the basic search box.
     * Checks that you can type in the select box and pick the proper result.
     */
    public function testCommunicationPropertySimpleSearch()
    {
        //start up a new session, going to the communication new page
        $this->session->visit('http://localhost:8000/app_test.php/communication/new');
        // Get the page
        $page = $this->session->getPage();

        $searchBox = $page->find("css",".ui.search.dropdown input.search");

        //type into the searchbox
        $searchBox->setValue("456 West Street");

        $searchBox->keyPress('s');
        $this->session->wait(1000);

        //Test that the filtered results contains the desired result
        $searchResults = $page->find("css",".menu.transition.visible .item.selected");

        //check that the search results show up
        $this->assertTrue($searchResults->isVisible());
        $this->assertContains("456 West Street",$searchResults->getHtml());

        //only result should be 456 West Street, so click it
        $searchResults->click();

        //check that the form field contains the property charlton legs
        $formField = $page->find("css", "#appbundle_communication_property");
        $this->assertEquals(17, $formField->getValue());


    }

    protected function tearDown()
    {
        // After the test has been run, make sure to stop the session so you don't run into problems
        $this->session->stop();

        // Delete the user from the database
        $stmt = $this->em->getConnection()->prepare("DELETE FROM User");
        $stmt->execute();
        $stmt = $this->em->getConnection()->prepare('DELETE FROM Property');
        $stmt->execute();
        $stmt = $this->em->getConnection()->prepare('DELETE FROM Address');
        $stmt->execute();
        $this->em->close();
        $this->em = null; //avoid memory meaks
    }
}