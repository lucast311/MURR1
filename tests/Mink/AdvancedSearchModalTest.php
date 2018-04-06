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
 * This test uses mink for browser based front-end testing of the javascript used in story 24c
 */
class AdvancedSerachModalTest extends WebTestCase
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
     * a modal will appear instead of a popup window. I don't test closing the modal here
     * since I will have a test later that tests the different methods for closing the modal.
     */
    function testModalAppears()
    {
        //start up a new session, and navigate to a communication's edit page
        $this->session->visit('http://localhost:8000/app_test.php/communication/2/edit');
        // Get the page
        $page = $this->session->getPage();

        // modal isn't visible when the page loads
        $this->assertFalse($page->find('css', "#propertyModal")->isVisible());

        // click the Advanced Search button
        $page->find('named', array('button', 'Advanced Search'))->click();

        // wait for the modal to appear
        $this->session->wait(1000);

        // make sure the modal is visible
        $this->assertTrue($page->find('css', "#propertyModal")->isVisible());
    }

    /**
     * story 24c
     * This will test that a user can still enter into a search query into the
     * modal window's search field, and have the table of results update to only
     * show properties that match the query.
     */
    function testSearchFieldWorksInsideModalWindow()
    {
        //start up a new session, and navigate to a communication's edit page
        $this->session->visit('http://localhost:8000/app_test.php/communication/2/edit');
        // Get the page
        $page = $this->session->getPage();

        // click the Advanced Search button
        $page->find('named', array('button', 'Advanced Search'))->click();

        // wait for the modal to appear
        $this->session->wait(1000);

        // get the search field
        $searchField = $page->find('css', '.prompt #searchBox');

        // enter a value into the search field
        $searchField->setValue("Cond");
        $searchField->keyPress("o");

        // get all the table rows in the results table
        $propertySearchTableRows = $page->findAll('css', 'div#propertyModal table tr');

        // for every row that isn't the first (header row)
        for ($i = 1; $i < sizeof($propertySearchTableRows); $i++)
        {
            // check that the text contained within that row contains the text we searched for
            $this->assertTrue(strpos($propertySearchTableRows[$i]->getText(), "Condo") !== False);
        }
    }

    /**
     * story 24c
     * This will test that the user can click on one of the results in the modal table
     * to pick a property for the select box.
     */
    function testSelectableResultRowsInModalTable()
    {
        //start up a new session, and navigate to a communication's edit page
        $this->session->visit('http://localhost:8000/app_test.php/communication/2/edit');
        // Get the page
        $page = $this->session->getPage();

        // open the modal
        $page->find('named', array('button', 'Advanced Search'))->click();

        // wait for the modal to appear
        $this->session->wait(1000);

        // get all the table rows in the results table
        $propertySearchTableRows = $page->findAll('css', 'div#propertyModal table tr');

        // Before we click the row, take the id of the property we clicked
        $id = $propertySearchTableRows[1]->getAttribute("data-id");

        // click on the row of the property whose id we stored
        $propertySearchTableRows[1]->click();

        // wait for modal to disappear
        $this->session->wait(1000);

        // make sure the modal has disappeared
        $this->assertFalse($page->find('css', "#propertyModal")->isVisible());

        // check that the value of the select box is now populated with the address of the property that was clicked
        $this->assertEquals($page->find('css',"#appbundle_communication_property")->getValue(), $id);
    }

    /**
     * story 24c
     *
     * NOTE: THIS TEST MAY BE REMOVED SINCE IT HAS ALSO BEEN FIXED IN ANOTHER STORY WHILE I WAS CODING THIS
     *
     * This will test that the user cannot select a property by clicking on
     * the header row of the modal table.
     */
    function testHeaderRowNotSelectable()
    {
        //start up a new session, and navigate to a communication's edit page
        $this->session->visit('http://localhost:8000/app_test.php/communication/2/edit');
        // Get the page
        $page = $this->session->getPage();

        // get the value of the select box when the page loads to make sure the value doesn't change later
        $currentProperty = $page->find('css',"#appbundle_communication_property")->getValue();

        // click the Advanced Search button
        $page->find('named', array('button', 'Advanced Search'))->click();

        // get all the table rows in the results table
        $propertySearchTableRows = $page->findAll('css', 'div#propertyModal table tr');

        // click on the header row of the modal table
        $propertySearchTableRows[0]->click();

        // get the value of the select box after the header row has been clicked
        $propertyAfterClicking = $page->find('css',"#appbundle_communication_property")->getValue();

        // make sure that the modal is still visible
        $this->assertTrue($page->find('css', "#propertyModal")->isVisible());

        // make sure that the property select box value is the same value as before the header was clicked
        $this->assertEquals($currentProperty, $propertyAfterClicking);
    }

    /**
     * story 24c
     * This will test closing the modal using the "Cancel" button
     */
    function testModalCancel()
    {
        //start up a new session, and navigate to a communication's edit page
        $this->session->visit('http://localhost:8000/app_test.php/communication/2/edit');
        // Get the page
        $page = $this->session->getPage();

        // click the Advanced Search button
        $page->find('named', array('button', 'Advanced Search'))->click();

        // wait for the modal to appear
        $this->session->wait(1000);

        // We don't need to check if the modal opened when we clicked the "Advanced Search" button
        // since we already has a test for that above.

        // close the modal
        $page->find('named', array('button', 'Cancel'))->click();

        // wait for the modal to disappear
        $this->session->wait(1000);

        // make sure the modal is no longer visible
        $this->assertFalse($page->find('css', "#propertyModal")->isVisible());

        // open the modal again
        $page->find('named', array('button', 'Advanced Search'))->click();

        // wait for the modal to appear
        $this->session->wait(1000);

        // find the description field so we can click on it and close the modal
        $page->find('css', '#appbundle_communication_description')->click();

        // make sure the modal is no longer visible
        $this->assertFalse($page->find('css', "#propertyModal")->isVisible());

    }

    protected function tearDown()
    {
        // After the test has been run, make sure to stop the session so you don't run into problems
        $this->session->stop();
    }
}
?>