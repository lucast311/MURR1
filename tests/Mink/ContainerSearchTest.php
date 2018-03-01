<?php

require_once 'vendor/autoload.php';
use DMore\ChromeDriver\ChromeDriver;
use Behat\Mink\Session;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use AppBundle\DataFixtures\ORM\LoadUserData;
use AppBundle\DataFixtures\ORM\LoadContainerData;

/**
 * This test uses mink for browser based front-end testing of the javascript used in story 12e
 */
class ContainerSearchTest extends WebTestCase
{
    private $driver;
    private $session;
    private $em;

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
     * Story 12e
     * Tests the funtionality for container search on the container search page.
     * Also test viewing from the search query
     */
    public function testContainerSearch()
    {
        $this->session->visit('http://localhost:8000/container/search');
        // Get the page
        $page = $this->session->getPage();
        // Search box
        $this->assertNotNull($page->find('named', array('id', "searchBox")));
        // Table headers
        $this->assertNotNull($page->find('named', array('content', "Serial")));
        $this->assertNotNull($page->find('named', array('content', "Frequency")));
        $this->assertNotNull($page->find('named', array('content', "Type")));
        $this->assertNotNull($page->find('named', array('content', "Size")));
        $this->assertNotNull($page->find('named', array('content', "Status")));
        $this->assertNotNull($page->find('named', array('content', "Property")));

        // Search for a Container
        $page->find('named', array('id', "searchBox"))->setValue("5W4G8UX");

        // Emulate a keyup to trigger the event that normally does a search.
        // Don't know why, it doesn't matter what character you press as it doesn't seem to go in the box anyways.
        $page->find('named', array('id', "searchBox"))->keyPress("s");

        // Make Mink wait for the search to complete. This has to be REALLY long because the dev server is slow.
        $this->session->wait(5000);

        //Assert that the proper container is returned by the search
        $this->assertNotNull($page->find('named', array('content', "5W4G8UX")));
        $this->assertNotNull($page->find('named', array('content', "Cart")));
        $this->assertNotNull($page->find('named', array('content', "4 yd")));
        $this->assertNotNull($page->find('named', array('content', "Active")));
        $this->assertNotNull($page->find('named', array('content', "KenLand")));

        // click the first link for one of the results
        $selectLink = $page->find('named', array('link', "View"));
        //Click the link
        $selectLink->click();

        // Refresh the page content so it reflects the window
        $page = $this->session->getPage();

        // Assert that we were redirected to the Container page
        $this->assertRegExp('/http:\/\/.+\/container\/3/', $this->session->getCurrentUrl());
    }

    /**
     * Story 12e
     * This tests the container front end search, asserting that if you search for a container does
     * not exist that you recieve the error message.
     */
    public function testContainerSearchNoResults()
    {
        // Go to the page
        $this->session->visit('http://localhost:8000/container/search');
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
        $this->session->visit('http://localhost:8000/container/search');
        // Get the page
        $page = $this->session->getPage();

        // Make a REALLY long string
        $longString = str_repeat("A", 101);

        // Search for a Container that has a string that's too long
        $page->find('named', array('id', "searchBox"))->setValue($longString);

        // Get the value back out of the text box to make sure it is only 100 characters (did not take the extra)
        $this->assertEquals(strlen($page->find('named', array('id', "searchBox"))->getValue()), 100);
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