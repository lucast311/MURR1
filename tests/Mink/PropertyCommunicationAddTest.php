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
class PropertyCommunicationAddTest extends WebTestCase
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

        $propertyLoader = new LoadPropertyData();
        $propertyLoader->load($this->em);

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
     * Story 11d
     * Checks the entire workflow of successfully adding a new communication directly from the property view page.
     */
    public function testPropertyNewCommunicationSuccess()
    {
        // Navigate to the property view page
        $this->session->visit('http://localhost:8000/app_test.php/property/161');
        // Get the page
        $page = $this->session->getPage();

        // find and assert that there is a new communication button
        $newCommunicationBtn = $page->find('named', array('button', "New Communication"));
        $this->assertNotNull($newCommunicationBtn);
        // Click the new communication button
        $newCommunicationBtn->click();

        // Assert that the new communication modal has appeared.
        $this->assertTrue($page->find('css', "div#communicationModal.ui.dimmer.modals.page.transition.active")->isVisible());

        // Fill out the new communication form
        $page->findById("communication_type")->setValue("Phone");
        $page->findById("communication_medium")->selectOption("Incoming");
        $page->findById("communication_contactName")->setValue("Mr. Man");
        $page->findById("communication_email")->setValue("mr.man@manson.ca");
        $page->findById("communication_phone")->setValue("123-456-7891");
        $page->findById("communication_category")->selectOption("Container");
        $page->findById("communication_description")->setValue("Mr. Man phoned and said there was a dune buggy stuck inside his recycling container. He wants it gone.");
        // Assert that the property has been auto populated
        $this->assertEquals($page->findById("communication_property")->getValue(), "123 Main Street");
        // Submit the form
        $page->findById("communication_add")->submit();

        // Assert that the new communication modal has vanished.
        $this->assertFalse($page->find('css', "div#communicationModal.ui.dimmer.modals.page.transition.active")->isVisible());

        // Once the page has reloaded, assert that the new communication is listed on the page
        $this->assertNotNull($page->find('named', array('content', "Phone")));
        $this->assertNotNull($page->find('named', array('content', "Incomming")));
        $this->assertNotNull($page->find('named', array('content', "Mr. Man")));
        $this->assertNotNull($page->find('named', array('content', "mr.man@manson.ca")));
        $this->assertNotNull($page->find('named', array('content', "123-456-7891")));
        $this->assertNotNull($page->find('named', array('content', "Mr. Man phoned and said there was a dune buggy stuck inside his recycling container. He wants it gone.")));

    }

    /**
     * Story 11d
     * Tests that when the new communication form is invalid, it actually shows the errors on the screen.
     */
    public function testPropertyNewCommunicationInvalid()
    {
        // Navigate to the property view page
        $this->session->visit('http://localhost:8000/app_test.php/property/161');
        // Get the page
        $page = $this->session->getPage();

        // find the new communication button
        $newCommunicationBtn = $page->find('named', array('button', "New Communication"));
        // Click the new communication button
        $newCommunicationBtn->click();

        // Assert that the new communication modal has appeared.
        $this->assertTrue($page->find('css', "div#communicationModal.ui.dimmer.modals.page.transition.active")->isVisible());

        // Fill out the new communication form
        // Do not set type, this should make the form invalid
        $page->findById("communication_medium")->selectOption("Incoming");
        $page->findById("communication_contactName")->setValue("Mr. Man");
        $page->findById("communication_email")->setValue("mr.man@manson.ca");
        $page->findById("communication_phone")->setValue("123-456-7891");
        $page->findById("communication_category")->selectOption("Container");
        $page->findById("communication_description")->setValue("Mr. Man phoned and said there was a dune buggy stuck inside his recycling container. He wants it gone.");

        // Once the page has reloaded, assert that the modal is still visible
        $this->assertTrue($page->find('css', "div#communicationModal.ui.dimmer.modals.page.transition.active")->isVisible());

        // assert that there is an error message on the page
        $this->assertNotNull($page->find('named', array('content', "Please select a type of communication")));

    }

    /**
     * Story 11d
     * Tests that clicking on a row of a listed communication will take you to it's view page
     */
    public function testCommunicationClickView()
    {
        // Navigate to the property view page
        $this->session->visit('http://localhost:8000/app_test.php/property/161');
        // Get the page
        $page = $this->session->getPage();

        // Click the desired communication (id 56 in this case)
        $page->find('named', array('content', "56"))->click();

        // Assert that we're on the right page
        $this->assertContains('/communication/view/56', $this->session->getCurrentUrl());
    }

    /**
     * Story 11d
     * Tests that clicking on a row of a listed container will take you to it's view page
     */
    public function testContactClickView()
    {
        // Navigate to the property view page
        $this->session->visit('http://localhost:8000/app_test.php/property/162');
        // Get the page
        $page = $this->session->getPage();

        // Click the desired contact
        $page->find('named', array('content', "Ken Kenson"))->click();

        // Assert that we're on the right page
        $this->assertContains('/contact/208', $this->session->getCurrentUrl());
    }

    /**
     * Story 11d
     * Tests that clicking on a row of a listed container will take you to it's view page
     */
    public function testContainerClickView()
    {
        // Navigate to the property view page
        $this->session->visit('http://localhost:8000/app_test.php/property/164');
        // Get the page
        $page = $this->session->getPage();

        // Click the desired contact (serial 888888)
        $page->find('named', array('content', "888888"))->click();

        // Assert that we're on the right page
        $this->assertContains('/container/32', $this->session->getCurrentUrl());
    }


    protected function tearDown()
    {
        // After the test has been run, make sure to stop the session so you don't run into problems
        $this->session->stop();

        // Delete the user from the database
        $stmt = $this->em->getConnection()->prepare("DELETE FROM User");
        $stmt->execute();
        $stmt = $this->em->getConnection()->prepare('DELETE FROM Property');
        $stmt->execute();
        $stmt = $this->em->getConnection()->prepare('DELETE FROM Address');
        $stmt->execute();
        $this->em->close();
        $this->em = null; //avoid memory meaks
    }
}