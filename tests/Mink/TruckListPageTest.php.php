<?php

require_once 'vendor/autoload.php';
use DMore\ChromeDriver\ChromeDriver;
use Behat\Mink\Session;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use AppBundle\Entity\Truck;
use AppBundle\DataFixtures\ORM\LoadTruckData;
use Tests\AppBundle\DatabasePrimer;

/**
    This class tests all javascript functionality on the truck list page.
*/
class TruckListPageTest extends WebTestCase
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
        self::bootKernel();

        $this->em = static::$kernel->getContainer()
            ->get('doctrine')
            ->getManager();

        // Wipe database before beginning because tests seem to run into errors
        $stmt = $this->em->getConnection()->prepare('DELETE FROM truck');
        $stmt->execute();

        // Load a user into the DB
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

    // Search Tests
    /**
        40a Tests that the search box shows suggestions
    */
    public function testSearchSuggestions()
    {
    }
    /**
        40a Tests that the list is narrowed when a truck is searched for
    */
    public function testSearchFilters()
    {
    }


    // Save Button Tests
    /**
        40a Tests that the update button doesn't show up when the page is first loaded
    */
    public function testUpdateButton()
    {
        // Navigate to the new communication page
        $this->session->visit('http://localhost:8000/app_test.php/truck');
        // Get the page
        $page = $this->session->getPage();

        // Try to find the update button on the page
        // Assert that the update button does not show up
        $this->assertNull($page->find('css','#updates'));
    }
    /**
        40a Tests that the update button shows up when a truck's field is updated
    */
    public function testUpdateButtonDisplay()
    {
        // Navigate to the new communication page
        $this->session->visit('http://localhost:8000/app_test.php/truck');
        // Get the page
        $page = $this->session->getPage();

        // Add information to one of the fields

        // Update update button shows up
    }
    /**
        40a Tests that the update button actually updates the field when the Update button is pressed
    */
    public function testUpdateButtonUpdate()
    {
    }
    /**
        40a Tests that the revert button shows up when a truck's field is updated
    */
    public function testRevertButtonDisplay()
    {
    }
    /**
        40a Tests that the Revert button actually works when the revert button is pressed
    */
    public function testRevertButtonRevert()
    {
    }


    // Delete Button Tests
    /**
        40a Tests that the delete button displays an error message when clicked
    */
    public function testDeleteButtonMessage()
    {
    }
    /**
        40a Tests that the decline button doesn't delete the truck.
    */
    public function testDeleteButtonDecline()
    {
    }
    /**
        40a Tests that the accept button DOES delete the truck.
    */
    public function testDeleteButtonAccept()
    {
    }

}