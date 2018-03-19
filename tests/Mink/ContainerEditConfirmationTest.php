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

        // Click the unlock button
        $page->find('named', array('button', "Unlock"))->click();
    }

    /**
     * Story 12g
     * This makes sure that you can delete a container and get the confirmation page
     */
    public function testContainerDelete()
    {
        // Go to the edit page of a container
        $this->session->visit('http://localhost:8000/app_test.php/container/1/edit');
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