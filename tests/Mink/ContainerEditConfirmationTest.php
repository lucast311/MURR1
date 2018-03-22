<?php

require_once 'vendor/autoload.php';
use DMore\ChromeDriver\ChromeDriver;
use Behat\Mink\Session;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use AppBundle\DataFixtures\ORM\LoadUserData;
use AppBundle\DataFixtures\ORM\LoadContainerData;
use AppBundle\DataFixtures\ORM\LoadPropertyData;
use Tests\AppBundle\DatabasePrimer;

/**
 * This test uses mink for browser based front-end testing of the javascript used in story 12g
 */
class ContainerEditConfirmationTest extends WebTestCase
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
        // Load the user fixture so you can actually log in
        self::bootKernel();
        $this->em = static::$kernel->getContainer()
            ->get('doctrine')
            ->getManager();

        // Also load in the containers so there is something to edit
        $containerLoader = new LoadContainerData();
        $containerLoader->load($this->em);

        $encoder = static::$kernel->getContainer()->get('security.password_encoder');

        $propertyLoader = new LoadPropertyData();
        $propertyLoader->load($this->em);

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
     * Story 12g
     * Test if the container serial field is uneditable, then test the confirmation button,
     * Then test if that makes the container serial field editable.
     */
    public function testEditConfirmationButton()
    {
        // Go to the edit page of a container
        $this->session->visit('http://localhost:8000/app_test.php/container/1/edit');
        // Get the page
        $page = $this->session->getPage();

        // find the text field for the serial number, and get its HTML
        $serialField = $page->find("css","#appbundle_container_containerSerial")->getHtml();

        // check that the field cannot be edited
        $this->assertContains('disabled', $serialField);

        // Click the unlock button
        $page->find('named', array('button', "Unlock"))->click();

        // check that the field can be edited
        $this->assertNotContains('disabled', $serialField);
    }

    /**
     * Story 12g
     * Test that a user can enter a new valid serial number, and be redirected to the current container's view page
     */
    public function testEnterNewValidSerial()
    {
        // Go to the edit page of a container
        $this->session->visit('http://localhost:8000/app_test.php/container/1/edit');
        // Get the page
        $page = $this->session->getPage();

        // Click the unlock button
        $page->find('named', array('button', "Unlock"))->click();

        // find the text field for the serial number, and set its value to the serial we're testing
        $serialField = $page->find("css","#appbundle_container_containerSerial")->setValue(123456);

        // Click the save button
        $page->find('named', array('button', "Save"))->click();

        // wait for the save action to complete
        $this->session->wait(5000);

        // get the first table on the page, and that table's 3rd row
        $table = $page->find("css", "table:first-child");
        $tableRow = $table->find("css", "tr:nth-child(3)");

        // check that the found row contains teh serial that we added above
        $this->assertContains("123456", $tableRow->getHTML());
    }

    /**
     * Story 12g
     * This makes sure that you can open the delete modal and confirm the delete
     */
    public function testContainerDeleteSuccess()
    {
        // Go to the edit page of a container
        $this->session->visit('http://localhost:8000/app_test.php/container/1/edit');
        // Get the page
        $page = $this->session->getPage();

        // Click the delete button
        $page->find('named', array('button', "Delete"))->click();

        // Make sure a modal pops up
        $this->assertTrue($page->find('css', "div.ui.dimmer.modals.page.transition.active")->isVisible());

        // Click the delete button inside the modal
        $page->find('css', 'div.ui.red.ok.inverted.button')->click();

        // wait for the delete action
        $this->session->wait(2000);

        // find the header for the "Container Search" page to make sure that we have been redirected
        $searchHeader = $page->find("css", "h2");

        // check that we have been redirected to the "Container Search" page
        $this->assertContains("Container Search", $searchHeader->getHtml());

        // Go back to the edit page of the container we just removed
        $this->session->visit('http://localhost:8000/app_test.php/container/1/edit');

        // Get the page
        $page = $this->session->getPage();

        // make sure that the container no longer exists
        $this->assertContains("Container does not exist", $page->getHtml());
    }

    /**
     * Story 12g
     * This makes sure that you can open the delete modal and cancel the delete
     */
    public function testContainerDeleteCancel()
    {
        // Go to the edit page of a container
        $this->session->visit('http://localhost:8000/app_test.php/container/1/edit');
        // Get the page
        $page = $this->session->getPage();

        // Click the delete button
        $page->find('named', array('button', "Delete"))->click();

        // Make sure a modal pops up
        $this->assertTrue($page->find('css', "div.ui.dimmer.modals.page.transition.active")->isVisible());

        // Click the cancel button inside the modal
        $page->find('css', 'div.ui.cancel.inverted.button')->click();

        // Make sure the modal is no longer visable
        $this->assertFalse($page->find('css', "div.ui.dimmer.modals.page.transition.active")->isVisible());

        // get the first table on the page, and that table's first row
        $table = $page->find("css", "table:first-child");
        $tableRow = $table->find("css", "tr:first-child");

        // check that the id matches the id that we navigated to
        $this->assertContains("1", $tableRow->getHtml());
    }

    /**
     * Story 12g
     * Test that the edit container page's dropdown list contains all the proper values
     */
    public function testStatusDropdownContainsAllOptions()
    {
        // Go to the edit page of a container
        $this->session->visit('http://localhost:8000/app_test.php/container/1/edit');
        // Get the page
        $page = $this->session->getPage();

        // Click on the select box so it opens
        $page->find('css', ".select2-selection, .select2-selection--single")->click();

        // Check that the select box contains all the right options
        $this->assertContains("--Please Select a Type--", $page->find('css', ".select2-results")->getHtml());
        $this->assertContains("Active", $page->find('css', ".select2-results")->getHtml());
        $this->assertContains("Inactive", $page->find('css', ".select2-results")->getHtml());
        $this->assertContains("Contaminated", $page->find('css', ".select2-results")->getHtml());
        $this->assertContains("Inaccessible", $page->find('css', ".select2-results")->getHtml());
        $this->assertContains("Owerflowing", $page->find('css', ".select2-results")->getHtml());
        $this->assertContains("Garbage Tip Requested", $page->find('css', ".select2-results")->getHtml());
        $this->assertContains("Garbage Tip Authorized", $page->find('css', ".select2-results")->getHtml());
        $this->assertContains("Garbage Tip Denied", $page->find('css', ".select2-results")->getHtml());
        $this->assertContains("Garbage Tip Scheduled", $page->find('css', ".select2-results")->getHtml());
    }

    /**
     * Story 12g
     * Test that the ten most recently changed containers are displayed in order.
     */
    public function testTenMostRecentRecordsDisplayed()
    {
        // Go to the edit page of a container
        $this->session->visit('http://localhost:8000/app_test.php/container/1/edit');
        // Get the page
        $page = $this->session->getPage();

        // get the first table on the page
        $table = $page->find("css", "table:first-child");

        // Check that the search table has only 10 records all in order
        $this->assertContains("QWERTY10", $table->find("css", "tr:nth-child(2)")->getHtml());
        $this->assertContains("QWERTY9", $table->find("css", "tr:nth-child(3)")->getHtml());
        $this->assertContains("QWERTY8", $table->find("css", "tr:nth-child(4)")->getHtml());
        $this->assertContains("QWERTY7", $table->find("css", "tr:nth-child(5)")->getHtml());
        $this->assertContains("QWERTY6", $table->find("css", "tr:nth-child(6)")->getHtml());
        $this->assertContains("QWERTY5", $table->find("css", "tr:nth-child(7)")->getHtml());
        $this->assertContains("QWERTY4", $table->find("css", "tr:nth-child(8)")->getHtml());
        $this->assertContains("QWERTY3", $table->find("css", "tr:nth-child(9)")->getHtml());
        $this->assertContains("QWERTY2", $table->find("css", "tr:nth-child(10)")->getHtml());
        $this->assertContains("QWERTY1", $table->find("css", "tr:nth-child(11)")->getHtml());
    }

    protected function tearDown()
    {
        // After the test has been run, make sure to stop the session so you don't run into problems
        $this->session->stop();

        // Delete the user from the database
        $stmt = $this->em->getConnection()->prepare("DELETE FROM User");
        $stmt->execute();
        $stmt = $this->em->getConnection()->prepare('DELETE FROM Container');
        $stmt->execute();
        $stmt = $this->em->getConnection()->prepare('DELETE FROM Property');
        $stmt->execute();
        $stmt = $this->em->getConnection()->prepare('DELETE FROM Address');
        $stmt->execute();
        $this->em->close();
        $this->em = null; //avoid memory meaks
    }
}