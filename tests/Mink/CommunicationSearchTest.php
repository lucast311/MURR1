<?php

require_once 'vendor/autoload.php';
use DMore\ChromeDriver\ChromeDriver;
use Behat\Mink\Session;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use AppBundle\DataFixtures\ORM\LoadUserData;
use AppBundle\DataFixtures\ORM\LoadCommunicationData;
use AppBundle\DataFixtures\ORM\LoadPropertyData;
use Tests\AppBundle\DatabasePrimer;

/**
 * This test uses mink for browser based front-end testing of the javascript used in story 12e
 */
class CommunicationSearchTest extends WebTestCase
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

        // Also load in the communications so there is something to search for
        $containerLoader = new LoadCommunicationData();
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
     * Story 10a
     * Tests the funtionality for communication search on the container search page.
     * Also test viewing from the search query
     */
    public function testContainerSearch()
    {
        $this->session->visit('http://localhost:8000/app_test.php/communication/search');
        // Get the page
        $page = $this->session->getPage();
        // Search box
        $this->assertNotNull($page->find('named', array('id', "searchBox")));
        // Table headers
        $this->assertNotNull($page->find('named', array('content', "Date")));
        $this->assertNotNull($page->find('named', array('content', "Type")));
        $this->assertNotNull($page->find('named', array('content', "Direction")));
        $this->assertNotNull($page->find('named', array('content', "Name")));
        $this->assertNotNull($page->find('named', array('content', "Phone")));
        $this->assertNotNull($page->find('named', array('content', "Email")));
        $this->assertNotNull($page->find('named', array('content', "Notes")));

        // Search for a communication
        $page->find('named', array('id', "searchBox"))->setValue("Phone");

        // Emulate a keyup to trigger the event that normally does a search.
        // Don't know why, it doesn't matter what character you press as it doesn't seem to go in the box anyways.
        $page->find('named', array('id', "searchBox"))->keyPress("s");

        // Make Mink wait for the search to complete. This has to be REALLY long because the dev server is slow.
        $this->session->wait(5000);

        //Assert that the proper communication is returned by the search
        $this->assertNotNull($page->find('named', array('content', "2018-01-01")));
        $this->assertNotNull($page->find('named', array('content', "Phone")));
        $this->assertNotNull($page->find('named', array('content', "Incoming")));
        $this->assertNotNull($page->find('named', array('content', "Ken")));
        $this->assertNotNull($page->find('named', array('content', "111-111-1111")));
        $this->assertNotNull($page->find('named', array('content', "email@email.com")));
        $this->assertNotNull($page->find('named', array('content', "Its a bin")));

        // click the first link for the desired result
        $selectLink = $page->find('css', "table.ui.selectable.celled.table tbody tr")->find('named', array('content', "Phone"));
        //Click the link
        $selectLink->click();

        // Refresh the page content so it reflects the window
        $page = $this->session->getPage();

        // Assert that we were redirected to the communication page
        $this->assertContains('/communication/1', $this->session->getCurrentUrl());
    }

    /**
     * Story 10a
     * This tests the communication front end search, asserting that if you search for a communication does
     * not exist that you recieve the error message.
     */
    public function testContainerSearchNoResults()
    {
        // Go to the page
        $this->session->visit('http://localhost:8000/app_test.php/communication/search');
        // Get the page
        $page = $this->session->getPage();

        // Search for a communication that we know should not exist
        $page->find('named', array('id', "searchBox"))->setValue("QWERTYUIOP123456789");

        // Emulate a keyup to trigger the event that normally does a search.
        // Don't know why, it doesn't matter what character you press as it doesn't seem to go in the box anyways.
        $page->find('named', array('id', "searchBox"))->keyPress("s");

        // Make Mink wait for the search to complete. This has to be REALLY long because the dev server is slow.
        $this->session->wait(8000);

        // Assert that the error message is displayed on the page
        $this->assertNotNull($page->find('named', array('content', "No results found")));
    }

    /**
     * Story 10a
     * Tests that when entering characters in the communication search box, no more than 100 characters
     * may be entered.
     */
    public function testQueryTooLong()
    {
        // Go to the page
        $this->session->visit('http://localhost:8000/app_test.php/communication/search');
        // Get the page
        $page = $this->session->getPage();

        // Make a REALLY long string
        $longString = str_repeat("A", 101);

        // Search for a communication that has a string that's too long
        $page->find('named', array('id', "searchBox"))->setValue($longString);

        // Get the value back out of the text box to make sure it is only 100 characters (did not take the extra)
        $this->assertEquals(strlen($page->find('named', array('id', "searchBox"))->getValue()), 100);
    }

    /**
     * Story 10a
     * This will test the functionality regarding the autocomplete drop down.
     */
    public function testAutoComplete()
    {
        $this->session->visit('http://localhost:8000/app_test.php/communication/search');
        // Get the page
        $page = $this->session->getPage();
        // Search box
        $this->assertNotNull($page->find('named', array('id', "searchBox")));
        // Search for a communication
        $page->find('named', array('id', "searchBox"))->setValue("P");

        // Make Mink wait for the search to complete. This has to be REALLY long because the dev server is slow.
        $this->session->wait(5000);

        // Make sure autocomplete options show up (select on the CSS)
        $this->assertTrue($page->find('css', ".results.transition")->isVisible());
        // Results we expect back
        $expectedResults = array("Phone", "In Person");
        // Make sure we get the results we expect
        foreach ($page->findAll('css', ".result .content .title") as $result)
        {
            $this->assertTrue(in_array($result->getText(), $expectedResults));
        }

        // Click Phone (should be the first result), and make sure it goes into the search box
        $page->find('css', ".result .content .title")->click();
        $this->assertEquals($page->find('named', array('id', "searchBox"))->getValue(), "Phone");

        $this->session->wait(500);

        // Assert the complete went away
        $this->assertFalse($page->find('css', "div.results.transition")->isVisible());
    }

    /**
     * Story 10a
     * This makes sure that you can delete a communication and get the confirmation page
     */
    public function testCommunicationDeleteOnView()
    {
        // Go to the view page of a communication
        $this->session->visit('http://localhost:8000/app_test.php/communication/1');

        // Get the page
        $page = $this->session->getPage();

        // Click the delete button
        $page->find('named', array('button', "Delete"))->click();
        $this->session->wait(500);
        // Make sure a modal pops up
        $this->assertTrue($page->find('css', ".ui.dimmer.modals.page.transition.visible.active")->isVisible());

        // Click the remove button
        $page->find('named', array('button', "Remove"))->click();

        // wait for the delete action
        $this->session->wait(2000);

        // check that we did get redirected to the search page
        $this->assertTrue($this->session->getCurrentUrl() == 'http://localhost:8000/app_test.php/communication/search');

        // find the header for the "communication Search" page
        $searchHeader = $page->find("css", "h2");

        // compare the header we find, with the one we know the search page contains
        $this->assertContains("Communication Search", $searchHeader->getHtml());

        // Go back to the view page of the communication we just removed
        $this->session->visit('http://localhost:8000/app_test.php/communication/1');

        // Get the page
        $page = $this->session->getPage();

        // make sure that the communication no longer exists
        $this->assertContains("The specified communication ID could not be found", $page->getHtml());
    }

    /**
     * Story 10a
     * This makes sure that you can delete a communication and get the confirmation page
     */
    public function testCommunicationDeleteOnEdit()
    {
        // Go to the edit page of a communication
        $this->session->visit('http://localhost:8000/app_test.php/communication/2/edit');

        // Get the page
        $page = $this->session->getPage();

        // Click the delete button
        $page->find('named', array('button', "Delete"))->click();
        // Make sure a modal pops up
        $this->assertTrue($page->find('css', ".ui.dimmer.modals.page.transition.visible.active")->isVisible());

        // Click the remove button
        $page->find('named', array('button', "Remove"))->click();

        // wait for the delete action
        $this->session->wait(2000);

        // check that we did get redirected to the search page
        $this->assertTrue($this->session->getCurrentUrl() == 'http://localhost:8000/app_test.php/communication/search');

        // find the header for the "communication Search" page
        $searchHeader = $page->find("css", "h2");

        // compare the header we find, with the one we know the search page contains
        $this->assertContains("Communication Search", $searchHeader->getHtml());

        // Go back to the view page of the communication we just removed
        $this->session->visit('http://localhost:8000/app_test.php/communication/2');

        // Get the page
        $page = $this->session->getPage();

        // make sure that the communication no longer exists
        $this->assertContains("The specified communication ID could not be found", $page->getHtml());
    }

    /**
     * Story 12g
     * This makes sure that you can open the delete modal and cancel the delete
     */
    public function testCommunicationDeleteCancel()
    {
        // Go to the edit page of a communication
        $this->session->visit('http://localhost:8000/app_test.php/communication/2');
        // Get the page
        $page = $this->session->getPage();

        // get the page header before we open the delete modal
        $communicationEditHeaderBefore = $page->find("css", "h1")->getText();

        // Click the delete button
        $page->find('named', array('button', "Delete"))->click();

        // Make sure a modal pops up
        $this->assertTrue($page->find('css', "#removeModal")->isVisible());

        // Click the cancel button inside the modal
        $page->find('named', array('button', "Cancel"))->click();

        $this->session->wait(1000);

        // Make sure the modal is no longer visable
        $this->assertFalse($page->find('css', "#removeModal")->isVisible());

        // get the page header after we close the delete modal (should contain the container serial)
        $communicationEditHeaderAfter = $page->find("css", "h1")->getText();

        // make sure that we are on the same communication view page, by comparing the two page headers
        $this->assertTrue($communicationEditHeaderBefore == $communicationEditHeaderAfter);
    }

    /**
     * Story 12g
     * This makes sure that you can open the delete modal and cancel the delete
     */
    public function testCommunicationDeleteCancelOnEdit()
    {
        // Go to the edit page of a communication
        $this->session->visit('http://localhost:8000/app_test.php/communication/2/edit');
        // Get the page
        $page = $this->session->getPage();

        // get the page header before we open the delete modal
        $communicationEditHeaderBefore = $page->find("css", "h1")->getText();

        // Click the delete button
        $page->find('named', array('button', "Delete"))->click();

        // Make sure a modal pops up
        $this->assertTrue($page->find('css', "#removeModal")->isVisible());

        // Click the cancel button inside the modal
        $page->find('named', array('button', "Cancel"))->click();

        $this->session->wait(1000);

        // Make sure the modal is no longer visable
        $this->assertFalse($page->find('css', "#removeModal")->isVisible());

        // get the page header after we close the delete modal
        $communicationEditHeaderAfter = $page->find("css", "h1")->getText();

        // make sure that we are on the same communication view page, by comparing the two page headers
        $this->assertTrue($communicationEditHeaderBefore == $communicationEditHeaderAfter);
    }

    protected function tearDown()
    {
        // After the test has been run, make sure to stop the session so you don't run into problems
        $this->session->stop();

        // Delete the user from the database
        $stmt = $this->em->getConnection()->prepare("DELETE FROM User");
        $stmt->execute();
        $stmt = $this->em->getConnection()->prepare('DELETE FROM Communication');
        $stmt->execute();
        $stmt = $this->em->getConnection()->prepare('DELETE FROM Property');
        $stmt->execute();
        $stmt = $this->em->getConnection()->prepare('DELETE FROM Address');
        $stmt->execute();
        $this->em->close();
        $this->em = null; //avoid memory meaks
    }
}