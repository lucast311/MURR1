<?php

require_once 'vendor/autoload.php';
use DMore\ChromeDriver\ChromeDriver;
use Behat\Mink\Session;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * This test uses mink for browser based front-end testing of the javascript used in story 4e
 */
class PropertySearchPopupTest extends WebTestCase
{
    private $driver;

    protected function setUp()
    {
        // Create a driver
        $this->driver = new ChromeDriver("http://localhost:9222",null, "localhost:8000");
        // Create a session and pass it the driver
        $session = new Session($this->driver);

        //Log the user in
        // Start the session
        $session->start();

        // Try to visit a page
        $session->visit('http://localhost:8000/login');
        // Get the current page
        $page = $session->getPage();
        // Fill out the login form
        $page->findById("username")->setValue("admin");
        $page->findById("password")->setValue("password");
        // Submit the form
        $page->find('named', array('id_or_name', "login"))->submit();

    }

    public function testMink()
    {


    }
}