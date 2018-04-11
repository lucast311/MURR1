<?php
namespace Tests\Mink;
require_once 'vendor/autoload.php';
use DMore\ChromeDriver\ChromeDriver;
use Behat\Mink\Session;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use AppBundle\DataFixtures\ORM\LoadUserData;

use AppBundle\Entity\Property;
use AppBundle\Entity\Contact;
use Doctrine\Common\Collections\ArrayCollection;
use Tests\AppBundle\DatabasePrimer;
use AppBundle\DataFixtures\ORM\LoadContactData;
use AppBundle\DataFixtures\ORM\LoadPropertyData;
/**
 * PropertyContactRemoveTest short summary.
 *
 * PropertyContactRemoveTest description.
 *
 * @version 1.0
 * @author cst201
 */
class PropertyContactRemoveTest extends WebTestCase
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
     * 4L
     * Test that you can browse to the Property page
     */
    public function testBrowsePage()
    {
        //start up a new session, going to the Property Charlton Arms
        $this->session->visit('http://localhost:8000/app_test.php');
        // Get the page
        $page = $this->session->getPage();

        $this->session->wait(1000);

        //A: CHANGED IN S40A -AB
        //click the contacts page
        $propertyBtn = $page->find('xpath', $this->session->getSelectorsHandler()
            ->selectorToXpath('xpath',"//a[contains(@href, 'property/search')]"));

        $propertyBtn->click();

        $this->session->wait(2000);

        //get the search bar
        $searchBox = $page->find("css","#searchBox");

        //Type in legs
        $searchBox->setValue("arm");
        $searchBox->keyPress("s");

        // Make Mink wait for the search to complete. This has to be REALLY long because the dev server is slow.
        $this->session->wait(5000);

        //Need to use named search to find button based on its content
        $viewLink = $page->find("named",array("content", "Charlton Arms"));
        //$viewLink = $page->find("css", "table > tbody > tr:first-child > td");

        $viewLink->click();

        $this->session->wait(1000);

        $pageContent = $page->getHtml();

        //check that expected content is on the page
        $this->assertContains("View Property", $pageContent);
        $this->assertContains("Charlton Arms",$pageContent);
        $this->assertContains("Townhouse Condo",$pageContent);
        $this->assertContains("Property Contacts",$pageContent);

        //check that the form is on the page
        $this->assertNotNull($page->find("css","form[name=appbundle_contactToProperty]"));

        //check that the table is on the page
        $this->assertNotNull($page->find("css","#associatedContacts"));

        //a contact that is on this contact
        $this->assertContains("Kenson, Ken", $pageContent);
    }

    /**
     * Story 4L
     * Tests that when you click the remove button, a modal is displayed
     */
    public function testRemoveButtonShowsModal()
    {
        //start up a new session, going to the Property Charlton Arms
        $this->session->visit('http://localhost:8000/app_test.php/property/4');

        $page= $this->session->getPage();

        //find the button with the ID of the remove button
        $this->assertNotNull($page->find("css", "#rmb4"));

        //assert that the modal is not 'active'
        $removeModal = $page->find('css','#removeModal');
        $this->assertFalse($removeModal->isVisible());

        //click on the button
        $removeButton = $page->find("css", "#rmb4");
        $removeButton->click();

        $this->session->wait(1000);

        //test that the modal now appears
        $removeModal = $page->find("css",".ui.dimmer.modals.page.transition.active");
        $this->assertTrue($removeModal->isVisible());
    }

    /**
     * Story 4L
     * Tests that a user can remove a contact from a property
     */
    public function testRemoveContactFromPropertyAccept()
    {

        //start up a new session, going to the Property Charlton Arms
        $this->session->visit('http://localhost:8000/app_test.php/property/4');

        $page = $this->session->getPage();
        $this->session->wait(1000);

        //click on the remove button
        $removeButton = $page->find("css", "#rmb4");
        $removeButton->click();

        //click the okay button
        $okayButton = $page->find("css", "#btnAccept");
        $okayButton->click();

        $this->session->wait(10000, "document.readyState === 'complete'");
        //assert that Kenson is no longer on the page
        $this->assertNotContains("Kenson, Ken", $page->find("css","#associatedContacts")->getHtml());

    }

    /**
     * Story 4L
     * Tests that a user can cancel removing a contact from a property
     */
    public function testRemoveContactFromPropertyCancel()
    {

        //start up a new session, going to the Property Charlton Arms
        $this->session->visit('http://localhost:8000/app_test.php/property/4');

        $this->session->wait(1000);

        $page = $this->session->getPage();

        $this->session->wait(1000);

        //click on the button
        $removeButton = $page->find("css", "#rmb4");
        $removeButton->click();

        //click the okay button
        $okayButton = $page->find("css", "#btnDecline");
        $okayButton->click();

        $this->session->wait(10000, "document.readyState === 'complete'");
        //assert that Testman is no longer on the page
        $this->assertContains("Kenson, Ken", $page->find("css","#associatedContacts")->getHtml());
    }

    /**
     * Story 4L
     * Test that a user can add a new association with the dropdown list
     */
    public function testAddContactToPropertyWithDropdown()
    {
        //start up a new session, going to the Property Charlton Arms
        $this->session->visit('http://localhost:8000/app_test.php/property/4');
        // Get the page
        $page = $this->session->getPage();

        $this->session->wait(10000);

        $dropDown = $page->find("css", ".ui.search.dropdown input.search");
        $dropDown->setValue("Kenson");

        //Test that the filtered results contains charlton legs
        $searchResults = $page->find("css",".menu.transition.visible .item.selected");

        $searchResults->click();

        //find add button
        $addBtn = $page->find("css", "#appbundle_contactToProperty_Add");
        $addBtn->click();
        $this->session->wait(10000, "document.readyState === 'complete'");


        $this->assertContains("Kenson, Ken", $page->find("css","#associatedContacts")->getHtml());
    }

    /**
     * Story 4L
     * Test that the add modal is not on the page until the "Advanced Search" button is clicked
     */
    public function testAddPopupIsShownOnlyAfterAdvancedSearchIsClicked()
    {

        //start up a new session, going to the Property Charlton Arms
        $this->session->visit('http://localhost:8000/app_test.php/property/4');
        // Get the page
        $page = $this->session->getPage();



        //assert modal is not active
        $this->assertNull($page->find("css","#addModal.active"));

        $this->session->wait(1000);

        //click the advanced button
        $advancedSearchBtn = $page->find("css", "#advanced_contact_search_popup");
        $advancedSearchBtn->click();

        $this->session->wait(1000);

        $this->assertTrue( count($this->session->getWindowNames()) > 1);
    }

    /**
     * Story 4L
     * Test that a user can add a new association with the advanced modal
     */
    public function testAddContactToPropertyWithAdvancedModal()
    {
        //start up a new session, going to the Property Charlton Arms
        $this->session->visit('http://localhost:8000/app_test.php/property/3');
        // Get the page
        $page = $this->session->getPage();

        //click the advanced button
        $advancedSearchBtn = $page->find("css", "#advanced_contact_search_popup");

        // Get the names of all the windows
        $windowNames = $this->session->getWindowNames();
        // Take note of this current window so we can return to it later
        $originalWindow = $this->session->getWindowName();

        $this->session->wait(10000);

        // Click on the advanced search button
        $advancedSearchBtn->click();

        // WAIT for the page to load, otherwise it will be empty when mink tries to use it.
        $this->session->wait(4000);

        // Get the names of all the windows AGAIN, so we can figure out which one is new
        $newWindowNames = $this->session->getWindowNames();
        // Figure out which is new. The only new window should be at position 0 in the resulting array
        $popupWindowArray = array_diff($newWindowNames, $windowNames);

        // Switch to the first window in the array (should be the popup)
        $this->session->switchToWindow($popupWindowArray[0]);

        // Re-get the page, since we are in a new window
        $page = $this->session->getPage();

        $this->session->wait(2000);

        //find the search box
        $contactSearchBox = $page->find("css", "#searchBox");
        $contactSearchBox->setValue("Bill Smith");
        $contactSearchBox->keyPress("h");

        //wait for results
        $this->session->wait(2000);

        //click first select btn
        $viewLink = $page->find("named",array("content", "Smith"));
        $viewLink->click();

        $this->session->switchToWindow($originalWindow);
        $this->session->wait(1000);
        $page = $this->session->getPage();

        $page->find("css", "#appbundle_contactToProperty_Add")->click();

        $this->session->wait(1000);
        $page = $this->session->getPage();

        //check that the contact table has the added contact
        $contactTable = $page->find("css", "#associatedContacts");
        $this->assertContains('Smith, Bill', $contactTable->getHtml());

    }

    /**
     * Story 4L
     * Test that a user cannnot add a contact association that already exists
     */
    public function testCannotAddContactToPropertyThatIsAlreadyAdded()
    {
        //start up a new session, going to the Property Charlton Arms
        $this->session->visit('http://localhost:8000/app_test.php/property/4');
        // Get the page
        $page = $this->session->getPage();

        $this->session->wait(1000);

        //find the drop down and populate
        $dropDown = $page->find("css", "#appbundle_contactToProperty_contact");
        //$dropDown->click();
        $dropDown->setValue("Kenson");

        //click the add button
        $addBtn = $page->find('css', '#appbundle_contactToProperty_Add');
        $addBtn->click();

        //wait for results
        $this->session->wait(1000);

        //get the page and do it again
        $page = $this->session->getPage();

        //find the drop down and populate
        $dropDown = $page->find("css", "#appbundle_contactToProperty_contact");
        //$dropDown->click();
        $dropDown->setValue("Kenson");

        //click the add button
        $addBtn = $page->find('css', '#appbundle_contactToProperty_Add');
        $addBtn->click();

        //wait for results
        $this->session->wait(1000);

        $this->assertContains("This property is already associated to the selected contact", $page->find("css", "form .ui.message .item")->getHTML());
    }

    /**
     * 4L
     * test that a contact can be deleted
     */
    public function testDeletePropertyConfirm()
    {
        //start up a new session, going to the Property Charlton Arms
        $this->session->visit('http://localhost:8000/app_test.php/property/4');
        // Get the page
        $page = $this->session->getPage();

        $this->session->wait(1000);

        //find the delete button
        $deleteBtn = $page->find("css", "#deleteButton");
        $deleteBtn->click();

        $this->session->wait(1000);

        $page = $this->session->getPage();

        //confirm deletion
        $confirmDelete = $page->find('css', '#btnAccept');
        $confirmDelete->click();

        $this->session->wait(1000);

        //search for property 1
        $searchField = $page->find('css', '#searchBox');
        $searchField->setValue(333666999);
        $searchField->keyPress(9);

        //ensure property 1 does not exist on the page
        $this->assertNotContains("333666999", $page->getHtml());

    }

    /**
     * 4L
     * test that a contact can be deleted
     */
    public function testDeletePropertyCancel()
    {
        //start up a new session, going to the Property Charlton Arms
        $this->session->visit('http://localhost:8000/app_test.php/property/4');
        // Get the page
        $page = $this->session->getPage();

        $this->session->wait(1000);


        //find the delete button
        $deleteBtn = $page->find("css", "#deleteButton");
        $deleteBtn->click();

        $this->session->wait(1000);

        $page = $this->session->getPage();

        //confirm deletion
        $cancelDelete = $page->find('css', '#btnDecline');
        $cancelDelete->click();

        $this->session->wait(10000);

        $this->session->visit('http://localhost:8000/app_test.php/property/search');

        //search for property 1
        $searchField = $page->find('css', '#searchBox');
        $searchField->setValue(3593843);
        $searchField->keyPress(9);
        $this->session->wait(10000);
        //ensure property 1 does not exist on the page
        $this->assertContains("3593843", $page->getHtml());

    }


    protected function tearDown()
    {
        parent::tearDown();
        // After the test has been run, make sure to restart the session so you don't run into problems
        $this->session->stop();

        //Now wipe the database
        $stmt = $this->em->getConnection()->prepare('DELETE FROM Property');
        $stmt->execute();
        $stmt = $this->em->getConnection()->prepare('DELETE FROM Contact');
        $stmt->execute();
        $stmt = $this->em->getConnection()->prepare('DELETE FROM User');
        $stmt->execute();
    }
}