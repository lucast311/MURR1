<?php
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
        //start up a new session, going to the contact view page for Bill Jones (ID 24)
        $this->session->visit('http://localhost:8000/app_test.php');
        // Get the page
        $page = $this->session->getPage();

        //click the menu button
        $menu = $page->find("css","#menuBtn");
        $menu->click();

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
        $searchBox->setValue("leg");
        $searchBox->keyPress("s");

        // Make Mink wait for the search to complete. This has to be REALLY long because the dev server is slow.
        $this->session->wait(5000);

        //Need to use named search to find button based on its content
        $viewLink = $page->find("named",array("content", "Legs"));
        //$viewLink = $page->find("css", "table > tbody > tr:first-child > td");

        $viewLink->click();

        $this->session->wait(1000);

        $pageContent = $page->getHtml();

        //check that expected content is on the page
        $this->assertContains("View Property", $pageContent);
        $this->assertContains("Charlton Legs",$pageContent);
        $this->assertContains("House",$pageContent);
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

        //now that the data exists, go to the page
        //start up a new session
        $this->session->visit('http:://localhost:8000/app_test.php/property/1');
        //get the page
        $page = $this->session->getPage();
        //find the button with the ID of the remove button
        assertNotNull($page->find("css", "#rmConBtn1"));

        //assert that the modal is not 'active'
        $removeModal = $page->find('css','#removeModal');
        assertFalse($removeModal->isVisible());

        //click on the button
        $removeButton = $page->find("css", "#rmConBtn1");
        $removeButton->click();

        //test that the modal now appears
        $removeModal = $page->find("css","#cancelModal.active");
        assertTrue($removeModal->isVisible());
    }

    /**
     * Story 4L
     * Tests that a user can remove a contact from a property
     */
    public function testRemoveContactFromPropertyAccept()
    {

        //now that the data exists, go to the page
        //start up a new session
        $this->session->visit('http:://localhost:8000/app_test.php/property/1');
        //get the page
        $page = $this->session->getPage();

        //click on the remove button
        $removeButton = $page->find("css", "#rmb118");
        $removeButton->click();

        //click the okay button
        $okayButton = $page->find("css", "#btnAccept");
        $okayButton->click();

        $this->session->wait(10000, "document.readyState === 'complete'");
        //assert that Testman is no longer on the page
        assertNull($page->find("html", "Testman"));

    }

    /**
     * Story 4L
     * Tests that a user can cancel removing a contact from a property
     */
    public function testRemoveContactFromPropertyCancel()
    {

        //now that the data exists, go to the page
        //start up a new session
        $this->session->visit('http:://localhost:8000/app_test.php/property/1');
        //get the page
        $page = $this->session->getPage();

        //click on the button
        $removeButton = $page->find("css", "#rmb1");
        $removeButton->click();

        //click the okay button
        $okayButton = $page->find("css", "#cancelRmv.ui negative button");
        $okayButton->click();

        $this->session->wait(10000, "document.readyState === 'complete'");
        //assert that Testman is no longer on the page
        assertNotNull($page->find("html", "Testman"));
    }

    /**
     * Story 4L
     * Test that a user can add a new association with the dropdown list
     */
    public function testAddContactToPropertyWithDropdown()
    {
        //now that the data exists, go to the page
        //start up a new session
        $this->session->visit('http:://localhost:8000/app_test.php/property/1');
        //get the page
        $page = $this->session->getPage();

        $dropDown = $page->find("css", "#addContactDropDown");
        $dropDown->click();
        $page->fillField($dropDown, "Testman");

        //find add button
        $addBtn = $page->find("css", "#addContactBtn");
        $addBtn->click();
        $this->session->wait(10000, "document.readyState === 'complete'");


        assertContains("Testman", $page->find("css",".contacts associations")->getHtml());
    }

    /**
     * Story 4L
     * Test that the add modal is not on the page until the "Advanced Search" button is clicked
     */
    public function testAddModalIsShownOnlyAfterAdvancedSearchIsClicked()
    {

        //now that the data exists, go to the page
        //start up a new session
        $this->session->visit('http:://localhost:8000/property/1');
        //get the page
        $page = $this->session->getPage();

        //assert modal is not active
        assertNull($page->find("css","#addModal.active"));

        //click the advanced button
        $advancedSearchBtn = $page->find("css", "#advancedSearchBtn");
        $advancedSearchBtn->click();

        assertTrue($page->find("css","#addContactModal")->isVisible());
    }

    /**
     * Story 4L
     * Test that a user can add a new association with the advanced modal
     */
    public function testAddContactToPropertyWithAdvancedModal()
    {
        //start up a new session
        $this->session->visit('http:://localhost:8000/app_test.php/property/1');
        //get the page
        $page = $this->session->getPage();

        //click the advanced button
        $advancedSearchBtn = $page->find("css", "#advancedSearchBtn");
        $advancedSearchBtn->click();

        //find the search box
        $contactSearchBox = $page->find("css", "#contactSearchBox");
        $contactSearchBox->setValue("Testman");

        //wait for results
        $this->session->wait(10000);

        //click first select btn
        $selectBtn = $page->find("css", "#selectBtn1");
        $selectBtn->click();

        //wait for page because it will reload
        $this->session->wait(10000);

        //check that the contact table has the added contact
        $page = $this->session->getPage();
        $contactTable = $page->find("css", "#contactAssosiations");
        assertContains('Testman', $contactTable->getHtml());

    }

    /**
     * Story 4L
     * Test that a user cannnot add a contact association that already exists
     */
    public function testCannotAddContactToPropertyThatIsAlreadyAdded()
    {

        //now that the data exists, go to the page
        //start up a new session
        $this->session->visit('http:://localhost:8000/php_test.php/property/1');
        //get the page
        $page = $this->session->getPage();

        //click the advanced button
        $advancedSearchBtn = $page->find("css", "#advancedSearchBtn");
        $advancedSearchBtn->click();

        //find the search box
        $contactSearchBox = $page->find("css", "#ContactSearchBox");
        $contactSearchBox->setValue("Testman");

        //wait for results
        $this->session->wait(10000);

        //check that the page has a message that you cannot add the same contact twice
        $page = $this->session->getPage();

        assertContains("You cannot add the same contact more than once", $page->getHtml());
    }

    /**
     * 4L
     * test that a contact can be deleted
     */
    public function testDeleteContactConfirm()
    {
        $this->session->visit('http:://localhost:8000/php_test.php/property/1');

        $page = $this->session->getPage();

        $deleteBtn = $page->find("css", "#deleteBtn");
        $deleteBtn->click();

        $this->session->wait(1000);

        $page = $this->session->getPage();

        //search for property 1

        //ensure property 1 does not exist on the page


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