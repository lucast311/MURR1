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

        $encoder = static::$kernel->getContainer()->get('security.password_encoder');

        $userLoader = new LoadUserData($encoder);
        $userLoader->load(DatabasePrimer::$entityManager);
    }

    protected function setUp()
    {
        // Load the user fixture so you can actually log in
        self::bootKernel();
        $this->em = static::$kernel->getContainer()
            ->get('doctrine')
            ->getManager();

        $containerLoader = new LoadContainerData();
        $containerLoader->load($this->em);

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

        // find the text field for the serial number, and get its HTML (#appbundle_container_containerSerial is the input, not the div)
        $serialField = $page->find("css","#appbundle_container_containerSerial");

        // check that the field cannot be edited
        $this->assertTrue($serialField->hasAttribute('readonly'));

        // Click the unlock button
        $page->find('named', array('button', "Unlock"))->click();

        // check that the field can be edited
        $this->assertFalse($serialField->hasAttribute('readonly'));
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
        $this->session->wait(3000);

        // check that we did get redirected to the search page
        $this->assertTrue($this->session->getCurrentUrl() == 'http://localhost:8000/app_test.php/container/1');

        // get the first table on the page, and that table's 3rd row
        $table = $page->find("css", "table:first-child");
        $tableRow = $table->find("css", "tr:nth-child(1)");

        // check that the found row contains the serial that we added above
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
        $this->assertTrue($page->find('css', ".ui.dimmer.modals.page.transition.visible.active")->isVisible());

        // Click the delete button inside the modal
        $page->find('named', array('button', "Remove"))->click();

        // wait for the delete action
        $this->session->wait(2000);

        // check that we did get redirected to the search page
        $this->assertTrue($this->session->getCurrentUrl() == 'http://localhost:8000/app_test.php/container/search');


        // find the header for the "Container Search" page
        $searchHeader = $page->find("css", "h2");

        // compare the header we find, with the one we know the search page contains
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

        // get the page header before we open the delete modal (should contain the container serial)
        $containerEditHeaderBefore = $page->find("css", "#contentSeparator h2")->getText();

        // Click the delete button
        $page->find('named', array('button', "btnDelete"))->click();

        // Make sure a modal pops up
        $this->assertTrue($page->find('css', "#removeModal")->isVisible());

        // Click the cancel button inside the modal
        $page->find('named', array('button', "Cancel"))->click();

        $this->session->wait(1000);

        // Make sure the modal is no longer visable
        $this->assertFalse($page->find('css', "#removeModal")->isVisible());

        // get the page header after we close the delete modal (should contain the container serial)
        $containerEditHeaderAfter = $page->find("css", "#contentSeparator h2")->getText();

        // make sure that we are on the same container view page, by comparing the two page headers
        $this->assertTrue($containerEditHeaderBefore == $containerEditHeaderAfter);
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
        $page->find('css', '#appbundle_container_status')->click();
        //$page->find('css', '.select2-selection, .select2-selection--single')->click();

        // get the results
        //$results = $page->find('css', "div.menu.transition.visible");
        //$results = $page->find('css', ".select2-results");


        //$this->assertEquals(0, sizeof($results));

        // get an array of all the options in the results
        $options = $page->findAll('css', '#appbundle_container_status option');
        //$options = $results->findAll('css', 'option');

        // Check that the select box contains all the right options, and that they are in the correct order (alphabetical)
        $this->assertContains("--Please Select a Status--", $options[0]->getText());
        $this->assertContains("Active", $options[1]->getText());
        $this->assertContains("Contaminated", $options[2]->getText());
        $this->assertContains("Garbage Tip Authorized", $options[3]->getText());
        $this->assertContains("Garbage Tip Denied", $options[4]->getText());
        $this->assertContains("Garbage Tip Requested", $options[5]->getText());
        $this->assertContains("Garbage Tip Scheduled", $options[6]->getText());
        $this->assertContains("Inaccessible", $options[7]->getText());
        $this->assertContains("Inactive", $options[8]->getText());
        $this->assertContains("Overflowing", $options[9]->getText());
    }

    /**
     * Story 12g
     * Test that the edit container page's dropdown list contains all the proper values
     */
    public function testEditButtonOnView()
    {
        // Go to the edit page of a container
        $this->session->visit('http://localhost:8000/app_test.php/container/1');
        // Get the page
        $page = $this->session->getPage();

        $this->session->wait(1000);

        // Click the edit button
        $page->find('css', '#btnEdit')->click();

        $this->session->wait(1000);

        $this->assertTrue($this->session->getCurrentUrl() == 'http://localhost:8000/app_test.php/container/1/edit');
    }

    protected function tearDown()
    {
        // After the test has been run, make sure to stop the session so you don't run into problems
        $this->session->stop();

        // Delete the user from the database
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