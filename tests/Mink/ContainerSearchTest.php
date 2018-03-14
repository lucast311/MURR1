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
 * This test uses mink for browser based front-end testing of the javascript used in story 12e
 */
class ContainerSearchTest extends WebTestCase
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

        // Also load in the containers so there is something to search for
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
     * Story 12e
     * Tests the funtionality for container search on the container search page.
     * Also test viewing from the search query
     */
    public function testContainerSearch()
    {
        $this->session->visit('http://localhost:8000/app_test.php/container/search');
        // Get the page
        $page = $this->session->getPage();
        // Search box
        $this->assertNotNull($page->find('named', array('id', "searchBox")));
        // Table headers
        $this->assertNotNull($page->find('named', array('content', "Serial")));
        $this->assertNotNull($page->find('named', array('content', "Frequency")));
        $this->assertNotNull($page->find('named', array('content', "Address")));
        $this->assertNotNull($page->find('named', array('content', "Location")));
        $this->assertNotNull($page->find('named', array('content', "Type")));
        $this->assertNotNull($page->find('named', array('content', "Size")));
        $this->assertNotNull($page->find('named', array('content', "Status")));

        // Search for a Container
        $page->find('named', array('id', "searchBox"))->setValue("123457");

        // Emulate a keyup to trigger the event that normally does a search.
        // Don't know why, it doesn't matter what character you press as it doesn't seem to go in the box anyways.
        $page->find('named', array('id', "searchBox"))->keyPress("s");

        // Make Mink wait for the search to complete. This has to be REALLY long because the dev server is slow.
        $this->session->wait(5000);

        //Assert that the proper container is returned by the search
        $this->assertNotNull($page->find('named', array('content', "123457")));
        $this->assertNotNull($page->find('named', array('content', "weekly")));
        $this->assertNotNull($page->find('named', array('content', "Test ST")));
        $this->assertNotNull($page->find('named', array('content', "South-west side")));
        $this->assertNotNull($page->find('named', array('content', "Cart")));
        $this->assertNotNull($page->find('named', array('content', "6 yd")));
        $this->assertNotNull($page->find('named', array('content', "Active")));

        // click the first link for the desired result
        $selectLink = $page->find('css', "table.ui.selectable.celled.table tbody tr")->find('named', array('content', "123457"));
        //Click the link
        $selectLink->click();

        // Refresh the page content so it reflects the window
        $page = $this->session->getPage();

        // Assert that we were redirected to the Container page
        $this->assertContains('/container/1', $this->session->getCurrentUrl());
    }

    /**
     * Story 12e
     * This tests the container front end search, asserting that if you search for a container does
     * not exist that you recieve the error message.
     */
    public function testContainerSearchNoResults()
    {
        // Go to the page
        $this->session->visit('http://localhost:8000/app_test.php/container/search');
        // Get the page
        $page = $this->session->getPage();

        // Search for a Container that we know should not exist
        $page->find('named', array('id', "searchBox"))->setValue("QWERTYUIOP123456789");

        // Emulate a keyup to trigger the event that normally does a search.
        // Don't know why, it doesn't matter what character you press as it doesn't seem to go in the box anyways.
        $page->find('named', array('id', "searchBox"))->keyPress("s");

        // Make Mink wait for the search to complete. This has to be REALLY long because the dev server is slow.
        $this->session->wait(5000);

        // Assert that the error message is displayed on the page
        $this->assertNotNull($page->find('named', array('content', "No results found")));
    }

    /**
     * Story 12e
     * Tests that when entering characters in the container search box, no more than 100 characters
     * may be entered.
     */
    public function testQueryTooLong()
    {
        // Go to the page
        $this->session->visit('http://localhost:8000/app_test.php/container/search');
        // Get the page
        $page = $this->session->getPage();

        // Make a REALLY long string
        $longString = str_repeat("A", 101);

        // Search for a Container that has a string that's too long
        $page->find('named', array('id', "searchBox"))->setValue($longString);

        // Get the value back out of the text box to make sure it is only 100 characters (did not take the extra)
        $this->assertEquals(strlen($page->find('named', array('id', "searchBox"))->getValue()), 100);
    }

    /**
     * Story 12e
     * This will test the functionality regarding the autocomplete drop down.
     */
    public function testAutoComplete()
    {
        $this->session->visit('http://localhost:8000/app_test.php/container/search');
        // Get the page
        $page = $this->session->getPage();
        // Search box
        $this->assertNotNull($page->find('named', array('id', "searchBox")));
        // Search for a Container
        $page->find('named', array('id', "searchBox"))->setValue("Ac");

        // Make Mink wait for the search to complete. This has to be REALLY long because the dev server is slow.
        $this->session->wait(5000);

        // Make sure autocomplete options show up (select on the CSS)
        $this->assertTrue($page->find('css', ".results.transition")->isVisible());
        // Results we expect back
        $expectedResults = array("Active", "Ack Street");
        // Make sure we get the results we expect
        foreach ($page->findAll('css', ".result .content .title") as $result)
        {
            $this->assertTrue(in_array($result->getText(), $expectedResults));
        }

        // Click Active (should be the first result), and make sure it goes into the search box
        $page->find('css', ".result .content .title")->click();
        $this->assertEquals($page->find('named', array('id', "searchBox"))->getValue(), "Active");

        $this->session->wait(500);

        // Assert the complete went away
        $this->assertFalse($page->find('css', "div.results.transition")->isVisible());
    }

    /**
     * Story 12e
     * This makes sure that you can delete a container and get the confirmation page
     */
    public function testContainerDelete()
    {
        // Go to the page of a container
        $this->session->visit('http://localhost:8000/app_test.php/container/1');
        // Get the page
        $page = $this->session->getPage();

        // Click the delete button
        $page->find('named', array('button', "Delete"))->click();
        // Make sure a modal pops up
        $this->assertTrue($page->find('css', "div.ui.dimmer.modals.page.transition.active")->isVisible());

        // Click the delete button
        $page->find('css', 'div.ui.red.ok.inverted.button')->click();

        // Make sure the container is gone from the list page
        $this->assertNull($page->find('named', array('content', "123457")));
        $this->assertNull($page->find('named', array('content', "weekly")));
        $this->assertNull($page->find('named', array('content', "Cosmo")));
        $this->assertNull($page->find('named', array('content', "South-west side")));
        $this->assertNull($page->find('named', array('content', "Cart")));
        $this->assertNull($page->find('named', array('content', "6 yd")));
        $this->assertNull($page->find('named', array('content', "Wheels")));
        $this->assertNull($page->find('named', array('content', "Active")));
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