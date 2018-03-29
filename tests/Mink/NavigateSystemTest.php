<?php


use DMore\ChromeDriver\ChromeDriver;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Tests\AppBundle\DatabasePrimer;
use Behat\Mink\Session;

use AppBundle\DataFixtures\ORM\LoadUserData;
use AppBundle\DataFixtures\ORM\LoadCommunicationData;
use AppBundle\DataFixtures\ORM\LoadContactData;
use AppBundle\DataFixtures\ORM\LoadContainerData;
use AppBundle\DataFixtures\ORM\LoadPropertyData;
use AppBundle\DataFixtures\ORM\LoadRouteData;
use AppBundle\DataFixtures\ORM\LoadTruckData;
/**
 * NavigateSystemTest short summary.
 *
 * NavigateSystemTest description.
 *
 * @version 1.0
 * @author cst244
 */
class NavigateSystemTest extends WebTestCase
{
    private $driver;
    private $session;
    private $em;

    public static function setUpBeforeClass()
    {
        self::bootKernel();
        DatabasePrimer::prime(self::$kernel);

        $encoder = static::$kernel->getContainer()->get('security.password_encoder');

        //Load ALL fixtures (so that there is stuff to browse to)
        $userLoader = new LoadUserData($encoder);
        $userLoader->load(DatabasePrimer::$entityManager);

        $communicationLoader = new LoadCommunicationData();
        $communicationLoader->load(DatabasePrimer::$entityManager);

        $contactLoader = new LoadContactData();
        $contactLoader->load(DatabasePrimer::$entityManager);

        $containerLoader = new LoadContainerData();
        $containerLoader->load(DatabasePrimer::$entityManager);

        $propertyLoader = new LoadPropertyData();
        $propertyLoader->load(DatabasePrimer::$entityManager);

        $routeLoader = new LoadRouteData();
        $routeLoader->load(DatabasePrimer::$entityManager);

        $truckLoader = new LoadTruckData();
        $truckLoader->load(DatabasePrimer::$entityManager);
    }

    protected function setUp()
    {
        // get the entity manager
        self::bootKernel();
        $this->em = static::$kernel->getContainer()
            ->get('doctrine')
            ->getManager();

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

    protected function tearDown()
    {
        // After the test has been run, make sure to stop the session so you don't run into problems
        $this->session->stop();
    }


    //NAVIGATION TESTS BELOW


}