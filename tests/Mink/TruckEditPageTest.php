<?php
namespace Tests\Mink;
require_once 'vendor/autoload.php';
use DMore\ChromeDriver\ChromeDriver;
use Behat\Mink\Session;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use AppBundle\DataFixtures\ORM\LoadTruckData;
use AppBundle\DataFixtures\ORM\LoadUserData;
use AppBundle\Entity\Truck;
use Tests\AppBundle\DatabasePrimer;

/**
    This tests functionality of the Truck Edit page
*/
class TruckEditPageTest extends WebTestCase
{
    private $driver;
    private $session;
    private $em;
    private $truck;

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

        $this->truck = (new Truck())
            ->setTruckId("00886")
            ->setType("Large");

        // Wipe database before beginning because tests seem to run into errors
        $stmt = $this->em->getConnection()->prepare('DELETE FROM truck');
        $stmt->execute();

        // Load a truck into the DB
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
        $page->pressButton('Log In');//replaced findButton('Log In')->click();
        // Wait for the page to load before trying to browse elsewhere
        $this->session->wait(10000, "document.readyState === 'complete'");


        // Load in trucks from our truck fixture
        $truckLoader = new LoadTruckData();
        $truckLoader->load($this->em);
    }


    //**ACCESSABILITY TEST**\\
    //STORY40A
    /**
        40a Tests that the edit page can be opened from truck util
     */
    public function testEditPageOpen()
    {
        // Navigate to the Truck List page
        $this->session->visit('http://localhost:8000/app_test.php/truck');
        // Get the page
        $page = $this->session->getPage();

        $this->session->wait(1000);
        // Grab the value of the first truck id
        $truckId = $page->findAll('css', '.truckId')[0]->getText();

        // Click the first edit button
        $page->findAll('css', '.editButton')[0]->click();

        $editPageTruckId = $page->findAll('css', '#contentSeparator h2')[0]->getText();

        $this->assertContains($truckId, $editPageTruckId);
    }

    //**EDIT TESTS**\\
    //STORY40A
    /**
        40a Tests that the edit page can change truckId
     */
    public function testEditPageTruckId()
    {
        // Navigate to the Truck List page
        $this->session->visit('http://localhost:8000/app_test.php/truck');
        // Get the page
        $page = $this->session->getPage();

        $this->session->wait(1000);
        // Grab the value of the first truck id
        $initialTruckId = $page->findAll('css', '.truckId')[0]->getText();

        // Click the first edit button
        $page->findAll('css', '.editButton')[0]->click();

        $page->findById("appbundle_truck_truckId")->setValue("1337");
        $page->findById("appbundle_truck_Save")->click();

        $editedTruckId = $page->findAll('css', '.truckId')[0]->getText();

        $this->assertFalse($initialTruckId == $editedTruckId);
    }

    //STORY40A
    /**
        Tests that the edit page can edit truck type
     */
    public function testEditPageTruckType()
    {
        // Navigate to the Truck List page
        $this->session->visit('http://localhost:8000/app_test.php/truck');
        // Get the page
        $page = $this->session->getPage();

        $this->session->wait(1000);
        // Grab the value of the first truck id
        $initialTruckType = $page->findAll('css', '.truckType')[0]->getText();

        // Click the first edit button
        $page->findAll('css', '.editButton')[0]->click();

        $page->findById("appbundle_truck_type")->setValue("TestType");

        $page->findById("appbundle_truck_Save")->click();

        $editedTruckType = $page->findAll('css', '.truckType')[0]->getText();

        $this->assertFalse($initialTruckType == $editedTruckType);
    }


    //**ERROR TEST**\\
    //STORY40A
    /**
        Tests that a truckId thats already in use wont have its data overwritten
     */
    public function testEditPageTruckIdError()
    {
        // Navigate to the Truck List page
        $this->session->visit('http://localhost:8000/app_test.php/truck');
        // Get the page
        $page = $this->session->getPage();

        $this->session->wait(1000);
        // Grab the value of the first truck id
        $initialTruckId = $page->findAll('css', '.truckId')[0]->getText();
        $usedTruckId    = $page->findAll('css', '.truckId')[1]->getText();

        // Click the first edit button
        $page->findAll('css', '.editButton')[0]->click();

        $page->findById("appbundle_truck_truckId")->setValue("$usedTruckId");

        $page->findById("appbundle_truck_Save")->click();

        $errorMessage = $page->findAll('css', '.message')[0]->getText();

        $this->assertContains("The Truck ID \"$usedTruckId\" is already in use, reverted to \"$initialTruckId\".", $errorMessage);
    }





    /**
     * (@inheritDoc)
     */
    protected function tearDown()
    {
        parent::tearDown();
        // After the test has been run, make sure to restart the session so you don't run into problems
        $this->session->stop();

        //Now wipe the database
        $em = $this->em;
        $stmt = $em->getConnection()->prepare('DELETE FROM Truck');
        $stmt->execute();
        $stmt = $em->getConnection()->prepare('DELETE FROM User');
        $stmt->execute();
    }

}