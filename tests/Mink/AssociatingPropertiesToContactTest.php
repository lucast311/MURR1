<?php
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
        $propertyLoader->load(DatabasePrimer:$em);
    }

    protected function setUp()
    {
        // Load the user fixture so you can actually log in
        self::bootKernel();
        $this->em = static::$kernel->getContainer()
            ->get('doctrine')
            ->getManager();

        // Also load in the properties so there is something to search for
        $propertyLoader = new LoadPropertyData();
        $propertyLoader->load($this->em);

        //$encoder = static::$kernel->getContainer()->get('security.password_encoder');

        //$userLoader = new LoadUserData($encoder);
        //$userLoader->load($this->em);

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
     * Tests that you can use the simple search to find a value
     */
    public function testAddAssociationSimpleSearch()
    {
        //start up a new session, going to the contact view page for Bill Jones (ID 24)
        $this->session->visit('http://localhost:8000/app_test.php/contact/24');
        // Get the page
        $page = $this->session->getPage();

        $searchBox = $page->find("css","#propertySearch");

        //type charlton legs into the searchbox
        $searchBox->setValue("Charlton Legs");

        //Test that the filtered results contains charlton legs
        $searchResults = $page->find("css","#searchResults");
        $this->assertContains("Charlton Legs",$searchResults->getHtml());

        //only result shoudl be charlton legs, so click it
        $searchResults->click();

        //check that the form field contains the property charlton legs
        $formField = $page->find("form[name='appbundle_propertyToContact'] input[name='property']");
        $this->assertContains('Charlton Legs',$formField->getHtml());
    }

    /**
     * Story 4k
     * Tests that you can remove an associated property for a contact
     */
    public function testRemoveAssociationDecline()
    {
        //start up a new session, going to the contact view page for Bill Jones (ID 24)
        $this->session->visit('http://localhost:8000/app_test.php/contact/24');
        // Get the page
        $page = $this->session->getPage();

        //assert that the associated properties table contains Balla Highrize
        $associatedProperties = $page->find("css","#associatedProperties");
        $this->assertContains("Balla Highrize",$associatedProperties->getHtml());


        //the remove button from property with ID 2 (should be Balla Highrize)
        $removeButton = $page->find("css","#rmb1");

        $removeButton->click();

        //get the modal that will ask the user if they want to accept
        $promptModal = $page->find("css","#removeModal");

        //check that the modal is visible
        $this->assertContains("visible",$promptModal->getAttribute("class"));

        //get the accept button
        $acceptBtn = $page->find("css","#removeModal #btnDecline");

        //click the accept button
        $acceptBtn->click();

        $associatedProperties = $page->find("css","#associatedProperties");

        //assert that the associated properties table still contains Balla Highrize
        $this->assertContains("Balla Highrize",$associatedProperties->getHtml());
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
        $this->assertContains("Thug Muny Apts.",$associatedProperties->getHtml());


        //the remove button from property with ID 2 (should be Thug Muny Apts.)
        $removeButton = $page->find("css","#rmb2");

        $removeButton->click();

        //get the modal that will ask the user if they want to accept
        $promptModal = $page->find("css","#removeModal");

        //check that the modal is visible
        $this->assertContains("visible",$promptModal->getAttribute("class"));

        //get the accept button
        $acceptBtn = $page->find("css","#removeModal #btnAccept");

        //click the accept button
        $acceptBtn->click();

        $associatedProperties = $page->find("css","#associatedProperties");

        //assert that the associated properties table no longer has Thug Muny Apts.
        $this->assertNotContains("Thug Muny Apts.",$associatedProperties->getHtml());
    }
}
?>