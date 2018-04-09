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
        $userLoader->load(DatabasePrimer::$entityManager);

        //load contact and property data
        $contactLoader = new LoadContactData();
        $contactLoader->load(DatabasePrimer::$entityManager);

        $propertyLoader = new LoadPropertyData();
        $propertyLoader->load(DatabasePrimer::$entityManager);
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
        $this->session->visit('http://localhost:8000/app_test.php');
        // Get the page
        $page = $this->session->getPage();


        //A: CHANGED IN S40A -AB
        //click the contacts page
        $contactsBtn = $page->find('xpath', $this->session->getSelectorsHandler()
            ->selectorToXpath('xpath',"//a[contains(@href, 'contact/search')]"));

        $contactsBtn->click();

        $this->session->wait(6000);

        //get the search bar
        $searchBox = $page->find("css","#searchBox");

        //Type in bill jones
        $searchBox->setValue("Bill Jone");
        $searchBox->keyPress("s");

        // Make Mink wait for the search to complete. This has to be REALLY long because the dev server is slow.
        $this->session->wait(10000);

        //Need to use named search to find button based on its content
        //
        //$page->find('xpath', $this->session->getSelectorsHandler()->selectorToXpath('xpath', "//a[contains(@href, 'contact/24')]"))->click();
        //$page->find("css", "table > tbody > tr:first-child > td")->doubleClick();
        $page = $this->session->getPage();
        $page->find("named",array("content", "Jones"))->click();

        //$viewLink->;

        $this->session->wait(10000);

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
        $this->assertContains("1132 Illinois Avenue", $pageContent);
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

        $searchBox->keyPress('s');
        $this->session->wait(1000);

        //Test that the filtered results contains charlton legs
        $searchResults = $page->find("css",".menu.transition.visible .item.selected");

        //check that the search results show up
        $this->assertTrue($searchResults->isVisible());
        $this->assertContains("456 West Street",$searchResults->getHtml());

        //only result shoudl be charlton legs, so click it
        $searchResults->click();

        //check that the form field contains the property charlton legs
        $formField = $page->find("css", "#appbundle_propertyToContact_property");
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
        $this->session->visit('http://localhost:8000/app_test.php/contact/24');
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
        $this->session->wait(2000);

        // Get the names of all the windows AGAIN, so we can figure out which one is new
        $newWindowNames = $this->session->getWindowNames();
        // Figure out which is new. The only new window should be at position 0 in the resulting array
        $popupWindowArray = array_diff($newWindowNames, $windowNames);

        // Switch to the first window in the array (should be the popup)
        $this->session->switchToWindow($popupWindowArray[0]);

        // Re-get the page, since we are in a new window
        $page = $this->session->getPage();

        $this->session->wait(2000);

        // Search box
        $this->assertNotNull($page->find('css', "#searchBox"));

        $searchBox = $page->find('css', "#searchBox");

        // Search for a property
        $searchBox->setValue("456 West Stree");

        // Emulate a keyup to trigger the event that normally does a search.
        // Don't know why, it doesn't matter what character you press as it doesn't seem to go in the box anyways.
        $searchBox->keyPress("t");

        // Make Mink wait for the search to complete. This has to be REALLY long because the dev server is slow.
        $this->session->wait(5000);

        $resultTable = $page->find('css', ".ui.celled.table");
        $this->assertTrue($resultTable->isVisible());

        $tableHtml = $resultTable->getHtml();
        // Table headers
        $this->assertContains("Site ID", $tableHtml);
        $this->assertContains("Property", $tableHtml);
        $this->assertContains("Type", $tableHtml);
        $this->assertContains("Status", $tableHtml);
        $this->assertContains("Structure Id", $tableHtml);
        $this->assertContains("Units", $tableHtml);
        $this->assertContains("Neighbourhood", $tableHtml);

        // click the first link for one of the results (need to used named to search by content)
        $selectLink = $page->find('named', array('content', "333666999"));

        // Before we click the link, take the id of the property we clicked.
        $id = $selectLink->getParent()->getAttribute("data-id");
        //Click the link
        $selectLink->click();

        // Switch back to the original window
        $this->session->switchToWindow($originalWindow);

        // Refresh the page content so it reflects the window
        $page = $this->session->getPage();

        // Get the select box now and check that it has the right property in it (based on the id of the property that was clicked originally)
        $this->assertEquals($page->find('css',"#appbundle_propertyToContact_property")->getValue(), $id);
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

        $this->session->wait(1000);
        //assert that the associated properties table contains Balla Highrize
        $associatedProperties = $page->find("css","#associatedProperties");
        $this->assertContains("456 West Street",$associatedProperties->getHtml());

        //the remove button from property with ID 2 (should be Balla Highrize)
        $removeButton = $page->find("css","#rmb1");

        $removeButton->click();

        $this->session->wait(1000);

        //get the modal that will ask the user if they want to accept
        $promptModal = $page->find("css","#removeModal");

        //check that the modal is visible
        $this->assertTrue($promptModal->isVisible());

        //get the accept button
        $acceptBtn = $page->find("css","#removeModal #btnDecline");

        //click the accept button
        $acceptBtn->click();

        $this->session->wait(1000);

        $this->assertFalse($promptModal->isVisible());

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

        $this->session->wait(1000);

        //get the modal that will ask the user if they want to accept
        $promptModal = $page->find("css","#removeModal");

        //check that the modal is visible
        $this->assertTrue($promptModal->isVisible());

        //get the accept button
        $acceptBtn = $page->find("css","#removeModal #btnAccept");

        //click the accept button
        $acceptBtn->click();


        $associatedProperties = $page->find("css","#associatedProperties");

        //assert that the associated properties table no longer has Thug Muny Apts.
        $this->assertNotContains("726 East Street",$associatedProperties->getHtml());
    }

    /**
     * Story 4k
     * Tests that an error message appears whe a user attempts to add an invalid propety to a contact
     */
    public function testInvalidProperty()
    {
        //start up a new session, going to the contact view page for Bill Jones (ID 24)
        $this->session->visit('http://localhost:8000/app_test.php/contact/24');
        // Get the page
        $page = $this->session->getPage();

        // get the selectbox
        $formField = $page->find("css", "#appbundle_propertyToContact_property");

        // setting an invalid id
        $formField->setValue(999);

        // get the add button
        $addBtn = $page->find("css", "#appbundle_propertyToContact_Add");

        // click the add button
        $addBtn->click();

        // wait
        $this->session->wait(4000);

        $page = $this->session->getPage();

        // check that the error exists
        //$this->assertContains("This contact is already associated to the selected property", $page->find("css", ".ui.message")->getHtml());
    }

    /**
     * Story 4k
     * Tests that the user is reditrected to the appropriate property view page based on the row they click on in the "Property Roster" table
     */
    public function testClickablePropertyRosterRow()
    {
        //start up a new session, going to the contact view page for Bill Jones (ID 24)
        $this->session->visit('http://localhost:8000/app_test.php/contact/24');
        // Get the page
        $page = $this->session->getPage();

        // get a row
        $tableRow = $page->find('named', array('content', "333666999"));

        // click the row
        $tableRow->click();

        // wait
        $this->session->wait(2000);

        $page = $this->session->getPage();

        // check that user is redirected to the Property view page
        $this->assertContains("View Property", $page->find("css", "h2")->getHtml());
        $this->assertContains("333666999", $page->find("css", "table")->getHtml());
    }

    protected function tearDown()
    {
        // After the test has been run, make sure to stop the session so you don't run into problems
        $this->session->stop();
    }
}
?>