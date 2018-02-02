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
    private $session;

    protected function setUp()
    {
        // Create a driver
        $this->driver = new ChromeDriver("http://localhost:9222",null, "localhost:8000");
        // Create a session and pass it the driver
        $this->session = new Session($this->driver);
        
        //Log the user in
        // Start the session
        $this->session->start();

        // go to the login page
        $this->session->visit('http://localhost:8000/login');
        // Get the current page
        $page = $this->session->getPage();
        // Fill out the login form
        $page->findById("username")->setValue("admin");
        $page->findById("password")->setValue("password");
        // Submit the form
        $page->find('named', array('id_or_name', "login"))->submit();
        

    }

    public function testCommunicationPropertyAdvancedSearch()
    {
        // Start up a new session
        //$this->session = new Session($this->driver);
        //$this->session->start();
        // Navigate to the new communication page
        $this->session->visit('http://localhost:8000/communication/new');
        // Get the page
        $page = $this->session->getPage();
        // find and assert that there is an advanced search button
        $advancedSearchBtn = $page->find('named', array('button', "Advanced Search"));
        $this->assertNotNull($advancedSearchBtn);
        $this->assertEquals($advancedSearchBtn->getText(), "Advanced Search");

        // Click on the advanced search button
        $advancedSearchBtn->click();

        // NOTE: may need to switch to popup window here, unsure how Mink handles the popup.

        // Assert that the search page information exists
        $page = $this->session->getPage(); // May not need this
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

        // click the first link for one of the results
        $page->find('named', array('content', "Select"))->click();

        // May have to refresh the page, may not
        $page = $this->session->getPage(); // May not need this

        // Get the select box now and check that it has the right property in it
        $this->assertEquals($page->find('named', array('id', "communication_property"))->getValue(), "Charlton Arms");
    }

    protected function tearDown()
    {
        // After the test has been run, make sure to restart the session so you don't run into problems
        $this->session->restart();
    }
}