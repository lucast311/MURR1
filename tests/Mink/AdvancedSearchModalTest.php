<?php

namespace Tests\Mink;
require_once 'vendor/autoload.php';
use DMore\ChromeDriver\ChromeDriver;
use Behat\Mink\Session;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use AppBundle\DataFixtures\ORM\LoadUserData;
use AppBundle\DataFixtures\ORM\LoadCommunicationData;
use Tests\AppBundle\DatabasePrimer;

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

        //load communication data
        $communicationLoader = new LoadCommunicationData();
        $communicationLoader->load(DatabasePrimer::$entityManager);
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
     * story 24c
     * This will test that when the user uses the Advanced Search by clicking on the button,
     * a modal will appear instead of a popup window. It will also test that the modal disappears
     * when a user presses the "Cancel" button.
     */
    function testModalAppearsDisappears()
    {
        //start up a new session, and navigate to a communication's edit page
        $this->session->visit('http://localhost:8000/app_test.php/communication/2/edit');
        // Get the page
        $page = $this->session->getPage();

        // click the Advanced Search button
        $page->find('named', array('button', 'Advanced Search'))->click();

        // make sure the modal is visible
        $this->assertTrue($page->find('css', "#propertyModal")->isVisible());

        // close the modal
        $page->find('named', array('button', 'Cancel'))->click();

        $this->session->wait(1000);

        // make sure the modal is no longer visible
        $this->assertFalse($page->find('css', "#propertyModal")->isVisible());
    }

    /**
     * story 24c
     * This will test that a user can still enter into the search field and have
     * the table of results update to only show properties that match the query.
     */
    function testSearchFieldWorks()
    {
        //start up a new session, and navigate to a communication's edit page
        $this->session->visit('http://localhost:8000/app_test.php/communication/2/edit');
        // Get the page
        $page = $this->session->getPage();

        // click the Advanced Search button
        $page->find('named', array('button', 'Advanced Search'))->click();

        // get the search field
        $searchField = $page->find('css', '.prompt #searchBox');

        // enter a value into the search field
        $searchField->setValue("Cond");
        $searchField->keyPress("o");

        // get all the table rows in the results table
        $propertySearchTableRows = $page->findAll('css', 'div#propertyModal table tr');

        // check that the table has more than 11 rows (10 from most recent, and 1 for the header row)
        $this->assertTrue(sizeof($propertySearchTableRows) > 11);

        for ($i = 1; $i < sizeof($propertySearchTableRows); $i++)
        {
            $this->assertTrue($propertySearchTableRows[$i]->getText());
        }

    }

    protected function tearDown()
    {
        // After the test has been run, make sure to stop the session so you don't run into problems
        $this->session->stop();
    }
}
?>