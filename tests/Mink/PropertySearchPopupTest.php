<?php

require_once 'vendor/autoload.php';
use DMore\ChromeDriver\ChromeDriver;
use Behat\Mink\Session;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * This test uses mink for browser based front-end testing of the javascript used in story 4e
 */
class PropertySearchPopupTest extends WebTestCase
{
    private $driver;

    protected function setUp()
    {
        // Create a driver
        $this->driver = new ChromeDriver("http://localhost:9222",null, "localhost:8000");
        // Create a session and pass it the driver
        //$session = new Session($this->driver);
        /*
        //Log the user in
        // Start the session
        $session->start();

        // go to the login page
        $session->visit('http://localhost:8000/login');
        // Get the current page
        $page = $session->getPage();
        // Fill out the login form
        $page->findById("username")->setValue("admin");
        $page->findById("password")->setValue("password");
        // Submit the form
        $page->find('named', array('id_or_name', "login"))->submit();
        */

    }

    public function testCommunicationPropertyAdvancedSearch()
    {
        // Start up a new session
        $session = new Session($this->driver);
        $session->start();
        // Navigate to the new communication page
        $session->visit('http://localhost:8000/communication/new');
        // Get the page
        $page = $session->getPage();
        // find and assert that there is an advanced search button
        $advancedSearchBtn = $page->find('named', array('button', "Advanced Search"));
        $this->assertNotNull($advancedSearchBtn);
        $this->assertEquals($advancedSearchBtn->getText(), "Advanced Search");

        // Click on the advanced search button
        $advancedSearchBtn->click();

        // NOTE: may need to switch to popup window here, unsure how Mink handles the popup.

        // Assert that the search page information exists
        $page = $session->getPage(); // May not need this
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

        // CONTINUE HERE. CLICK THE LINK.
    }
}