<?php

namespace Tests\Mink;
require_once 'vendor/autoload.php';
use DMore\ChromeDriver\ChromeDriver;
use Behat\Mink\Session;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use AppBundle\DataFixtures\ORM\LoadUserData;
use AppBundle\DataFixtures\ORM\LoadPropertyData;
use Tests\AppBundle\DatabasePrimer;
use AppBundle\DataFixtures\ORM\LoadContactData;

/**
 * This test uses mink for browser based front-end testing of the javascript used in story 4k
 */
class AssociatingPropertiesToContactTest extends WebTestCase
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
        $userLoader->load(DatabasePrimer::$em);

        //load contact and property data
        $contactLoader = new LoadContactData();
        $contactLoader->load(DatabasePrimer::$em);

        $propertyLoader = new LoadPropertyData();
        $propertyLoader->load(DatabasePrimer::$em);
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

    /**
     * Story 4k
     * Tests that you can browse to the contact view page
     */
    public function testBrowsePage()
    {
        //start up a new session, going to the contact view page for Bill Jones (ID 24)
        $this->session->visit('http://localhost:8000/app_test.php/');
        // Get the page
        $page = $this->session->getPage();

        //click the menu button
        $menu = $page->find("css","#menuBtn");
        $menu->click();

        //click the contacts page
        $contactsBtn = $page->find("css","#contactsPage");

        $contactsBtn->click();

        //get the search bar
        $searchBox = $page->find("css","#searchBox");

        //Type in bill jones
        $searchBox->setValue("Bill Jone");
        $searchBox->keyPress("s");

        // Make Mink wait for the search to complete. This has to be REALLY long because the dev server is slow.
        $this->session->wait(5000);

        //Need to use named search to find button based on its content
        $viewLink = $page->find("named",array("link", "View"));

        $viewLink->click();

        $pageContent = $page->getHtml();

        //check that expected content is on the page
        $this->assertContains("View Contact", $pageContent);
        $this->assertContains("Bill",$pageContent);
        $this->assertContains("Jones",$pageContent);
        $this->assertContains("Property Roster",$pageContent);

        //check that the form is on the page
        $this->assertNotNull($page->find("css","form[name=appbundle_propertyToContact]"));

        //check that the table is on the page
        $this->assertNotNull($page->find("css","#associatedProperties"));

        //a property that is on this contact
        $this->assertContains("1132 Illinois Avenue",$pageContent);
    }

    /**
     * Story 4k
     * Tests that you can use the simple search to find a value
     */
    public function testAssociationSimpleSearch()
    {
        //start up a new session, going to the contact view page for Bill Jones (ID 24)
        $this->session->visit('http://localhost:8000/app_test.php/contact/24');
        // Get the page
        $page = $this->session->getPage();

        $searchBox = $page->find("css",".ui.search.dropdown input.search");

        //type charlton legs into the searchbox
        $searchBox->setValue("456 West Street");

        //Test that the filtered results contains charlton legs
        $searchResults = $page->find("css",".ui.search.dropdown menu.transition.visible item.active.filtered");

        //check that the search results show up
        $this->assertTrue($searchResults->isVisible());
        $this->assertContains("456 West Street",$searchResults->getHtml());

        //only result shoudl be charlton legs, so click it
        $searchResults->click();

        //check that the form field contains the property charlton legs
        $formField = $page->find("css", "form[name='appbundle_propertyToContact'] input[name='property']");
        $this->assertContains('456 West Street',$formField->getValue());
        $this->assertEquals(1, $formField->getValue());
    }

    /**
     * Story 4k
     * Tests all functionality related to the advanced property search on the contact view page.
     * Ensures the button is there, that it functions correctly, and that the chosen property is set in the search box.
     *
     * HEY! This test will not run if popups are not allowed in chrome. GO ALLOW POPUPS IN CHROME.
     */
    public function testAssociationAdvancedSearch()
    {
        // Navigate to the contact view page
        $this->session->visit('http://localhost:8000/app_test.php/contact/23');
        // Get the page
        $page = $this->session->getPage();

        // find and assert that there is an advanced search button
        $advancedSearchBtn = $page->find('css', "#advanced_property_search_popup");
        $this->assertNotNull($advancedSearchBtn);
        $this->assertEquals($advancedSearchBtn->getValue(), "Advanced Search");

        // Get the names of all the windows
        $windowNames = $this->session->getWindowNames();
        // Take note of this current window so we can return to it later
        $originalWindow = $this->session->getWindowName();

        // Click on the advanced search button
        $advancedSearchBtn->click();

        // WAIT for the page to load, otherwise it will be empty when mink tries to use it.
        $this->session->wait(1000);

        // Get the names of all the windows AGAIN, so we can figure out which one is new
        $newWindowNames = $this->session->getWindowNames();
        // Figure out which is new. The only new window should be at position 0 in the resulting array
        $popupWindowArray = array_diff($newWindowNames, $windowNames);

        // Switch to the first window in the array (should be the popup)
        $this->session->switchToWindow($popupWindowArray[0]);

        // Re-get the page, since we are in a new window
        $page = $this->session->getPage();

        // Search box
        $this->assertNotNull($page->find('css', "#searchBox"));

        $resultTable = $page->find('css', ".ui.celled.table");
        $this->assertTrue($resultTable->isVisible());

        $tableHtml = $resultTable->getHtml();
        // Table headers
        $this->assertContains($tableHtml, "Site ID");
        $this->assertContains($tableHtml, "Property");
        $this->assertContains($tableHtml, "Type");
        $this->assertContains($tableHtml, "Status");
        $this->assertContains($tableHtml, "Structure Id");
        $this->assertContains($tableHtml, "Units");
        $this->assertContains($tableHtml, "Neighbourhood");

        $searchBox = $page->find('css', "#searchBox");

        // Search for a property
        $searchBox->setValue("456 West Stree");

        // Emulate a keyup to trigger the event that normally does a search.
        // Don't know why, it doesn't matter what character you press as it doesn't seem to go in the box anyways.
        $searchBox->keyPress("t");

        // Make Mink wait for the search to complete. This has to be REALLY long because the dev server is slow.
        $this->session->wait(5000);

        // click the first link for one of the results (need to used named to search by content)
        $selectLink = $page->find('named', array('link', "Select"));

        // Before we click the link, take the id of the property we clicked.
        $id = $selectLink->getAttribute("data-id");
        //Click the link
        $selectLink->click();

        // Switch back to the original window
        $this->session->switchToWindow($originalWindow);

        // Refresh the page content so it reflects the window
        $page = $this->session->getPage();

        // Get the select box now and check that it has the right property in it (based on the id of the property that was clicked originally)
        $this->assertEquals($page->find('css',"#propertySearch")->getValue(), $id);
    }

    /**
     * Story 4k
     * Tests that you can cancel the removal of an associated property
     */
    public function testRemoveAssociationDecline()
    {
        //start up a new session, going to the contact view page for Bill Jones (ID 24)
        $this->session->visit('http://localhost:8000/app_test.php/contact/24');
        // Get the page
        $page = $this->session->getPage();

        //assert that the associated properties table contains Balla Highrize
        $associatedProperties = $page->find("css","#associatedProperties");
        $this->assertContains("456 West Street",$associatedProperties->getHtml());


        //the remove button from property with ID 2 (should be Balla Highrize)
        $removeButton = $page->find("css","#rmb1");

        $removeButton->click();

        //get the modal that will ask the user if they want to accept
        $promptModal = $page->find("css","#removeModal");

        //check that the modal is visible
        $this->assertContains("visible",$promptModal->getAttribute("class"));
        $this->assertTrue($promptModal->isVisible());

        //get the accept button
        $acceptBtn = $page->find("css","#removeModal #btnDecline");

        //click the accept button
        $acceptBtn->click();

        $associatedProperties = $page->find("css","#associatedProperties");

        //assert that the associated properties table still contains Balla Highrize
        $this->assertContains("456 West Street",$associatedProperties->getHtml());
    }

    /**
     * Story 4k
     * Tests that you can remove an associated property for a contact
     */
    public function testRemoveAssociationSuccess()
    {
        //start up a new session, going to the contact view page for Bill Jones (ID 24)
        $this->session->visit('http://localhost:8000/app_test.php/contact/24');
        // Get the page
        $page = $this->session->getPage();

        //assert that the associated properties table contains Thug Muny Apts.
        $associatedProperties = $page->find("css","#associatedProperties");
        $this->assertContains("726 East Street",$associatedProperties->getHtml());


        //the remove button from property with ID 2 (should be Thug Muny Apts.)
        $removeButton = $page->find("css","#rmb2");

        $removeButton->click();

        //get the modal that will ask the user if they want to accept
        $promptModal = $page->find("css","#removeModal");

        //check that the modal is visible
        $this->assertContains("visible",$promptModal->getAttribute("class"));
        $this->assertTrue($promptModal->isVisible());

        //get the accept button
        $acceptBtn = $page->find("css","#removeModal #btnAccept");

        //click the accept button
        $acceptBtn->click();

        $associatedProperties = $page->find("css","#associatedProperties");

        //assert that the associated properties table no longer has Thug Muny Apts.
        $this->assertNotContains("726 East Street",$associatedProperties->getHtml());
    }
}
?>