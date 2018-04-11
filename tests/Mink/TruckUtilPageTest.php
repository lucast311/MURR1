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
    This class tests functionality of the truck utility page.
*/
class TruckUtilPageTest extends WebTestCase
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

    //**Add Test**\\
    /**
     * 40a Test that a truck can be added to the system via the form
     */
    public function testAddTruck()
    {
        // Navigate to the Truck List page
        $this->session->visit('http://localhost:8000/app_test.php/truck');
        // Get the page
        $page = $this->session->getPage();
        $this->session->wait(1000);

        //add the truck via the form
        $page->findById("appbundle_truck_truckId")->setValue($this->truck->getTruckId());
        $page->findById("appbundle_truck_type")->setValue($this->truck->getType());
        $page->findById('appbundle_truck_Add')->click();

        //check that the new truck appears on the page
        $this->session->wait(2000);
        $this->assertEquals( $page->findAll('css', '.truckId')[0]->getText(), "".$this->truck->getTruckId()."");
    }

    //**Filter tests**\\
    /**
        40a Tests that the Filter box shows suggestions
    */
    //!!! ****IMPLEMENT IN S40B**** !!!//
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

        $truckIdItem = $page->findAll('css', '.truckId')[0]->getText();

        // search for something
        $page->findById("truckFilterBox")->setValue("002");

        // Emulate a keyup to trigger the event that normally does a search.
        $page->findById("truckFilterBox")->keyPress("s");

        // Make Mink wait for the search to complete. This has to be REALLY long because the dev server is slow.
        $this->session->wait(5000);

        // Assert that the first list item is different
        $this->assertNotEquals($truckIdItem, $page->findAll('css', '.truckId')[0]->getText());
    }

    //**Delete Tests**\\
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
        $this->session->wait(1000);

        // Assert that the delete modal is visible
        $this->assertTrue($page->find('css', '#removeModal')->isVisible());
        // Check for correct message
        $this->assertEquals($page->find('css','#removeModalMessage')->getText(), "Are you sure you want to remove Truck '000034'");
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

        $this->session->wait(1000);
        // Grab the value of the first truck id
        $truckIdItem = $page->findAll('css', '.truckId')[0]->getText();

        // Click the first delete button
        $page->findAll('css', '.removeButton')[0]->click();
        // Check that the delete modal is visible
        $this->session->wait(1000);
        $this->assertTrue($page->find('css','#removeModalMessage')->isVisible());

        // Click the cancel remove button
        $page->find('css', '#btnDecline')->click();

        // Check that the modal is not visible
        $this->session->wait(1000);
        $this->assertFalse($page->find('css','#removeModalMessage')->isVisible());

        // Check that The truck isn't removed
        $this->assertEquals( $page->findAll('css', '.truckId')[0]->getText(), $truckIdItem);
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
        $firstTruckValue = $page->findAll('css', '.truckId')[0]->getText();

        // Click the first delete button
        $page->findAll('css', '.removeButton')[0]->click();

        $this->session->wait(
                1000, "$('#removeModal').is(':visible')"
        );

        // Click the accept Button in Modal
        $page->find('css', '#btnAccept')->click();

        $this->session->wait(5000, "document.readyState === 'complete'");

        // Check that The truck is removed
        $this->assertFalse( strpos($page->getText(), $firstTruckValue) > 0 );
    }

    /**
        40C Tests that the filter search works as intended
     */
    public function testMoreFilterFunctionality()
    {
        // Navigate to the Truck List page
        $this->session->visit('http://localhost:8000/app_test.php/truck');
        // Get the page
        $page = $this->session->getPage();

        // Grab the value of the first truck id
        $initNumTrucks = count($page->findAll('css', '.truckId'));
        $firstTruckId = $page->findAll('css', '.truckId')[0]->getText();

        $filter = ($page->findAll('css', '.prompt')[0]);
        $filter->setValue("Sma");
        $filter->keyPress("s");

        $this->session->wait(5000);

        //assert the list was updated with filtered values
        $this->assertTrue($page->findAll('css', '.truckId')[0]->getText() != $firstTruckId);
        $this->assertTrue($page->findAll('css', '.truckType')[0]->getText() == "Small");
        $this->assertTrue($initNumTrucks>count($page->findAll('css', '.truckId')));

        $filter->setValue("");
        $filter->keyPress("s");

        $this->session->wait(5000);

        //assert the results go back to normal
        $this->assertTrue($page->findAll('css', '.truckId')[0]->getText() == $firstTruckId);
        $this->assertTrue($page->findAll('css', '.truckType')[0]->getText() == "Large");
        $this->assertTrue($initNumTrucks==count($page->findAll('css', '.truckId')));
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