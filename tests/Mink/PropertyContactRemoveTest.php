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
    }


    protected function setUp()
    {
        self::bootKernel();

        $this->em = static::$kernel->getContainer()
            ->get('doctrine')
            ->getManager();

        //Wipe database before beginning because tests seem to run into errors
        $stmt = $this->em->getConnection()->prepare('DELETE FROM Property');
        $stmt->execute();
        $stmt = $this->em->getConnection()->prepare('DELETE FROM Contact');
        $stmt->execute();
        $stmt = $this->em->getConnection()->prepare('DELETE FROM User');
        $stmt->execute();

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
        $this->session->visit('http://localhost:8000/login');
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
     * Story 4L
     * Tests that when you click the remove button, it shows the other two buttons instead
     */
    public function testRemoveButtonShowsModal()
    {
        //create a new property
        $property = new Property();

        $repo = $this->em->getRepository(Property::class);
        $repo->save($property);

        //create a new contact
        $contact = new Contact();
        $contact->setFirstName("Testman");
        $contact->setRole("Owner");

        //associate the two
        $arrayCollection = new ArrayCollection();
        $arrayCollection->add($contact);
        $property->setContacts($arrayCollection);
        //now that the data exists, go to the page
        //start up a new session
        $this->session->visit('http:://localhost:8000/property/view/1');
        //get the page
        $page = $this->session->getPage();
        //find the button with the ID of the remove button
        assertNotNull($page->find("css", "#rmConBtn1"));

        //assert that the modal is not 'active'
        assertNull($page->find("css","#cancelModal.active"));

        //click on the button
        $removeButton = $page->find("css", "#rmConBtn1");
        $removeButton->click();

        //test that the modal now appears
        assertNotNull($page->find("css","#cancelModal.active"));
    }

    /**
     * Story 4L
     * Tests that a user can remove a contact from a property
     */
    public function testRemoveContactFromPropertyAccept()
    {
        //create a new property
        $property = new Property();

        $repo = $this->em->getRepository(Property::class);
        $repo->save($property);

        //create a new contact
        $contact = new Contact();
        $contact->setFirstName("Testman");
        $contact->setRole("Owner");

        //associate the two
        $arrayCollection = new ArrayCollection();
        $arrayCollection->add($contact);
        $property->setContacts($arrayCollection);
        //now that the data exists, go to the page
        //start up a new session
        $this->session->visit('http:://localhost:8000/property/1');
        //get the page
        $page = $this->session->getPage();

        //click on the button
        $removeButton = $page->find("css", "#rmb1");
        $removeButton->click();

        //click the okay button
        $okayButton = $page->find("css", ".checkmark icon");
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
        //create a new property
        $property = new Property();

        $repo = $this->em->getRepository(Property::class);
        $repo->save($property);

        //create a new contact
        $contact = new Contact();
        $contact->setFirstName("Testman");
        $contact->setRole("Owner");

        //associate the two
        $arrayCollection = new ArrayCollection();
        $arrayCollection->add($contact);
        $property->setContacts($arrayCollection);
        //now that the data exists, go to the page
        //start up a new session
        $this->session->visit('http:://localhost:8000/property/1');
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
        //create a new property
        $property = new Property();

        $repo = $this->em->getRepository(Property::class);
        $repo->save($property);

        //create a new contact
        $contact = new Contact();
        $contact->setFirstName("Testman");
        $contact->setRole("Owner");

        //associate the two
        $arrayCollection = new ArrayCollection();
        $arrayCollection->add($contact);
        $property->setContacts($arrayCollection);
        //now that the data exists, go to the page
        //start up a new session
        $this->session->visit('http:://localhost:8000/property/1');
        //get the page
        $page = $this->session->getPage();

        $dropDown = $page->find("css", "#addContactDropDown");
        $dropDown->click();
        $page->fillField($dropDown, "Testman");

        //find add button
        $addBtn = $page->find("css", "#addContactBtn");
        $addBtn->click();
        $this->session->wait(10000, "document.readyState === 'complete'");


        assertContains("Testman", $page->find("css",".contacts associations"));
    }

    /**
     * Story 4L
     * Test that the add modal is not on the page until the "Advanced Search" button is clicked
     */
    public function testAddModalIsShownOnlyAfterAdvancedSearchIsClicked()
    {
        //create a new property
        $property = new Property();

        $repo = $this->em->getRepository(Property::class);
        $repo->save($property);

        //create a new contact
        $contact = new Contact();
        $contact->setFirstName("Testman");
        $contact->setRole("Owner");

        //associate the two
        $arrayCollection = new ArrayCollection();
        $arrayCollection->add($contact);
        $property->setContacts($arrayCollection);
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

        assertNotNull($page->find("css","#addModal.active"));
    }

    /**
     * Story 4L
     * Test that a user can add a new association with the advanced modal
     */
    public function testAddContactToPropertyWithAdvancedModal()
    {
        //create a new property
        $property = new Property();

        $repo = $this->em->getRepository(Property::class);
        $repo->save($property);

        //create a new contact
        $contact = new Contact();
        $contact->setFirstName("Testman");
        $contact->setRole("Owner");

        //start up a new session
        $this->session->visit('http:://localhost:8000/property/1');
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

        //click first select btn
        $selectBtn = $page->find("css", "#select1");
        $selectBtn->click();

        //wait for page because it will reload
        $this->session->wait(10000);

        //check that the contact table has the added contact
        $page = $this->session->getPage();
        $contactTable = $page->find("css", ".contact associations");
        assertContains('Testman', $contactTable->getHtml());

    }

    /**
     * Story 4L
     * Test that a user cannnot add a contact association that already exists
     */
    public function testCannotAddContactToPropertyThatIsAlreadyAdded()
    {
        //create a new property
        $property = new Property();

        $repo = $this->em->getRepository(Property::class);
        $repo->save($property);

        //create a new contact
        $contact = new Contact();
        $contact->setFirstName("Testman");
        $contact->setRole("Owner");

        //associate the two
        $arrayCollection = new ArrayCollection();
        $arrayCollection->add($contact);
        $property->setContacts($arrayCollection);
        //now that the data exists, go to the page
        //start up a new session
        $this->session->visit('http:://localhost:8000/property/1');
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