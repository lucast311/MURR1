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
        $page->find('named', array('id_or_name', "login"))->submit();
        // Wait for the page to load before trying to browse elsewhere
        $this->session->wait(10000, "document.readyState === 'complete'");


        // Load in trucks from our truck fixture
        $truckLoader = new LoadTruckData($encoder);
        $truckLoader->load($this->em);
    }

    // Search Tests
    /**
        40a Tests that the search box shows suggestions
    */
    public function testSearchSuggestions()
    {
        // Navigate to the Truck List page
        $this->session->visit('http://localhost:8000/app_test.php/truck');
        // Get the page
        $page = $this->session->getPage();

        // search for something
        $page->find('named', array('id', "filter"))->setValue("00886");

        // Emulate a keyup to trigger the event that normally does a search.
        $page->find('named', array('id', "searchBox"))->keyPress("s");

        // Make Mink wait for the search to complete. This has to be REALLY long because the dev server is slow.
        $this->session->wait(10000);
        // Check contents
        $this->assertNotNull( $page->find('css', 'result') );
    }
    /**
        40a Tests that the list is narrowed when a truck is searched for
    */
    public function testSearchFilters()
    {
        // Navigate to the Truck List page
        $this->session->visit('http://localhost:8000/app_test.php/truck');
        // Get the page
        $page = $this->session->getPage();

        $truckIdItem = $page->find('css', '.truckID')->getValue();

        // search for something
        $page->find('named', array('id', "filter"))->setValue("00886");

        // Emulate a keyup to trigger the event that normally does a search.
        $page->find('named', array('id', "searchBox"))->keyPress("s");

        // Make Mink wait for the search to complete. This has to be REALLY long because the dev server is slow.
        $this->session->wait(10000);

        $this->assertNotEqual($truckIdItem, $page->find('css', 'truckID')->getValue());
    }


    // Update Button Tests
    /**
        40a Tests that the update button doesn't show up when the page is first loaded
    */
    public function testUpdateButton()
    {
        // Navigate to the Truck List page
        $this->session->visit('http://localhost:8000/app_test.php/truck');
        // Get the page
        $page = $this->session->getPage();

        // Try to find the update button on the page
        // Assert that the update button does not show up
        $this->assertNull($page->find('css','.updates'));
    }
    /**
        40a Tests that the update button shows up when a truck's field is updated
    */
    public function testUpdateButtonDisplay()
    {
        // Navigate to the Truck List page
        $this->session->visit('http://localhost:8000/app_test.php/truck');
        // Get the page
        $page = $this->session->getPage();

        // Add information to the first truck field
        $page->find('css', '.truckID')->setValue("00887");

        // We may have to wait here depending on if mink goes too fast for the JS

        // Update button shows up
        $this->assertNotNull($page->find('css', '.updates'));
    }
    /**
        40a Tests that the update button is removed when pressed
    */
    public function testUpdateButtonRemoved()
    {
        // Navigate to the Truck List page
        $this->session->visit('http://localhost:8000/app_test.php/truck');
        // Get the page
        $page = $this->session->getPage();

        // Add information to the first truck field
        $page->find('css', '.truckID')->setValue("00887");

        // click the update button
        $page->find('css', '.updates')->click();

        // assert that the update button is no longer on the page
        $this->assertNull( $page->find('css', '.updates') );
    }
    /**
        40a Tests that the revert button shows up when a truck's field is updated
    */
    public function testRevertButtonDisplay()
    {
        // Navigate to the Truck List page
        $this->session->visit('http://localhost:8000/app_test.php/truck');
        // Get the page
        $page = $this->session->getPage();

        // Add information to the first truck field
        $page->find('css', '.truckID')->setValue("00887");

        // Ensure the revert button is there
        $this->assertNotNull( $page->find('css', '.reverts') );
    }
    /**
        40a Tests that the Revert button actually works when the revert button is pressed
    */
    public function testRevertButtonRevert()
    {
        // Navigate to the Truck List page
        $this->session->visit('http://localhost:8000/app_test.php/truck');
        // Get the page
        $page = $this->session->getPage();

        // Add information to the first truck field
        $truckIdItem = $page->find('css', '.truckID');
        $originalValue = $truckIdItem->getValue();

        $truckinfo->setValue("00887");

        // Click the first Revert Button
        $page->find('css', '.reverts')->click();

        // get the value of the text box again
        $this->assertEquals( $truckIdItem->getValue(), $originalValue);
    }


    // Delete Button Tests
    /**
        40a Tests that the delete button displays an error message when clicked
    */
    public function testDeleteButtonMessage()
    {
        // Navigate to the Truck List page
        $this->session->visit('http://localhost:8000/app_test.php/truck');
        // Get the page
        $page = $this->session->getPage();

        // Click the first delete button
        $page->find('css', '.deletes')->click();
        $this->assertNotNull( $page->find('css', '.deletesMessage'));
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
        $page->find('css', '.deletes')->click();

        // Click the Decline Button in Modal
        $page->find('css', '.declines')->click();

        $this->assertEqual( $page->find('css', '.truckID')->getValue(), $originalValue );
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
        $page->find('css', '.deletes')->click();

        // Click the accept Button in Modal
        $page->find('css', '.accepts')->click();

        $this->assertNotEqual( $page->find('css', '.truckID')->getValue(), $firstTruckValue  );
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