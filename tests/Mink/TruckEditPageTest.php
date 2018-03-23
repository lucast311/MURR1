<?php
namespace Tests\Mink;
require_once 'vendor/autoload.php';
use DMore\ChromeDriver\ChromeDriver;
use Behat\Mink\Session;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use AppBundle\DataFixtures\ORM\LoadTruckData;
use Tests\AppBundle\DatabasePrimer;


/**
    This page tests all javascript functionality on the Truck Edit page
*/
class TruckEditPageTest extends WebTestCase
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
        $page->findButton('Log In')->click();
        // Wait for the page to load before trying to browse elsewhere
        $this->session->wait(10000, "document.readyState === 'complete'");


        // Load in trucks from our truck fixture
        $truckLoader = new LoadTruckData($encoder);
        $truckLoader->load($this->em);
    }


    // 40a
    //public function TestDeleteModal()
    //{
    //    // Navigate to the Truck List page
    //    $this->session->visit('http://localhost:8000/app_test.php/truck/edit/1');
    //    // Get the page
    //    $page = $this->session->getPage();

    //    // Test that the Delete modal isn't visible
    //    $removeModal = $page->find('css', '#removeModal');
    //    $this->AssertFalse( $removeModal->isVisible());

    //    // Click the delete button on the page
    //    $page->find('css', 'remove').click();

    //    // Test that the delete modal is visible
    //    $this->assertTrue( $removeModal->isVisible() );
    //}






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