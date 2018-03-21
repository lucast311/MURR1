<?php

require_once 'vendor/autoload.php';
use DMore\ChromeDriver\ChromeDriver;
use Behat\Mink\Session;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use AppBundle\DataFixtures\ORM\LoadTruckData;
use AppBundle\DataFixtures\ORM\LoadUserData;
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


    //Filter tests
    /**
        40a Tests that the Filter box shows suggestions
    */
    //public function testFilterSuggestions()
    //{
    //    // Navigate to the Truck List page
    //    $this->session->visit('http://localhost:8000/app_test.php/truck');
    //    // Get the page
    //    $page = $this->session->getPage();

    //    // search for something
    //    $page->findById("truckFilterBox")->setValue("00886");

    //    // Emulate a keyup to trigger the event that normally does a search.
    //    $page->findById("truckFilterBox")->keyPress("s");

    //    // Make Mink wait for the search to complete. This has to be REALLY long because the dev server is slow.
    //    $this->session->wait(5000);
    //    // Check contents
    //    $this->assertNotNull( $page->find('css', '.result') ); //A: MIGHT NOT BE LOOKING FOR RESULT
    //}

    /**
        40a Tests that the list is narrowed when filtering on a trucks info
    */
    public function testFilters()
    {
        // Navigate to the Truck List page
        $this->session->visit('http://localhost:8000/app_test.php/truck');
        // Get the page
        $page = $this->session->getPage();

        $truckIdItem = $page->findAll('css', '.truckID')[0]->getValue();

        // search for something
        $page->findById("truckFilterBox")->setValue("00886");

        // Emulate a keyup to trigger the event that normally does a search.
        $page->findById("truckFilterBox")->keyPress("s"); 

        // Make Mink wait for the search to complete. This has to be REALLY long because the dev server is slow.
        $this->session->wait(10000);

        // Assert that the first list item is different
        $this->assertNotEqual($truckIdItem, $page->findAll('css', '.truckId')[0]->getValue());
    }

    // Delete Truck Tests
    /**
        40a Tests that the delete button displays a modal when clicked
    */
    public function testDeleteButtonMessage()
    {
        // Navigate to the Truck List page
        $this->session->visit('http://localhost:8000/app_test.php/truck');
        // Get the page
        $page = $this->session->getPage();

        // Click the first delete button
        $page->findAll('css', '.removeButton ')[0]->click();
        // Assert that the delete modal is visible
        $this->assertTrue($page->findAll('css', '#removeModal')[0]->isVisible());
    }

    /**
        40a Tests that the decline button doesn't delete the truck.
    */
    public function testDeleteButtonDecline()
    {
        // Navigate to the Truck List page
        $this->session->visit('http://localhost:8000/app_test.php/truck');
        // Get the page
        $page = $this->session->getPage();

        // Grab the value of the first truck id
        $truckIdItem = $page->find('css', '.truckID');
        $originalValue = $truckIdItem->getValue();

        // Click the first delete button
        $page->findAll('css', '.removeButton')[0]->click();
        // Check that the delete modal is visible
        $this->assertTrue($page->find('css', '#deletesMessage').isVisible());
        
        // Click the cancel remove button
        $page->findAll('css', '.btnDecline')[0]->click();

        // Check that the modal is not visible
        $this->assertFalse($page->find('css', '#deletesMessage').isVisible());

        // Check that the truck hasn't been removed
        $this->assertEqual( $page->findAll('css', '.truckID')[0]->getValue(), $originalValue);
    }

    /**
        40a Tests that the accept button DOES delete the truck.
    */
    public function testDeleteButtonAccept()
    {
        // Navigate to the Truck List page
        $this->session->visit('http://localhost:8000/app_test.php/truck');
        // Get the page
        $page = $this->session->getPage();

        // Grab the value of the first truck id
        $truckIdItem = $page->find('css', '.truckID');
        $firstTruckValue = $truckIdItem->getValue();

        // Click the first delete button
        $page->find('css', '.removeButton')->click();

        // Check that the delete modal is visible
        $this->assertTrue($page->find('css', '#removeModal').isVisible());

        // Click the accept Button in Modal
        $page->find('css', '#btnAccept')->click();

        // Check that the modal is not visible
        $this->assertFalse($page->find('css', '#removeModal').isVisible());

        // Check that The truck is removed
        $this->assertNotEqual( $page->find('css', '.truckID')->getValue(), $firstTruckValue );
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