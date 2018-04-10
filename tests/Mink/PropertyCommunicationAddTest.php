<?php

require_once 'vendor/autoload.php';
use DMore\ChromeDriver\ChromeDriver;
use Behat\Mink\Session;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use AppBundle\DataFixtures\ORM\LoadUserData;
use AppBundle\DataFixtures\ORM\LoadContainerData;
use AppBundle\DataFixtures\ORM\LoadPropertyData;
use AppBundle\DataFixtures\ORM\LoadCommunicationData;
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

        $communicationLoader = new LoadCommunicationData();
        $communicationLoader->load($this->em);

        $containerLoader = new LoadContainerData();
        $containerLoader->load($this->em);

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
        $this->session->visit('http://localhost:8000/app_test.php/property/1');
        // Get the page
        $page = $this->session->getPage();

        // find and assert that there is a new communication button
        $newCommunicationBtn = $page->find('css', "#newCommunication");
        $this->assertNotNull($newCommunicationBtn);
        // Click the new communication button
        $newCommunicationBtn->click();

        $this->session->wait(1000);

        // Assert that the new communication modal has appeared.
        $this->assertTrue($page->find('css', "#communicationModal")->isVisible());

        // Fill out the new communication form
        $page->find('css',"#appbundle_communication_type")->setValue("Phone");
        $page->find('css',"#appbundle_communication_medium_1")->click();
        $page->find('css',"#appbundle_communication_contactName")->setValue("Mr. Man");
        $page->find('css',"#appbundle_communication_contactEmail")->setValue("mr.man@manson.ca");
        $page->find('css',"#appbundle_communication_contactPhone")->setValue("123-456-7891");
        $page->find('css',"#appbundle_communication_category")->selectOption("Container");
        $page->find('css',"#appbundle_communication_description")->setValue("Mr. Man phoned and said there was a dune buggy stuck inside his recycling container. He wants it gone.");
        // Assert that the property has been auto populated
        $this->assertEquals($page->find('css',"#appbundle_communication_property")->getValue(), 1); //1 should be the ID of the property
        // Submit the form
        $page->find('css',"#communicationSubmit")->click();

        $this->session->wait(2000);

        // Assert that the new communication modal has vanished.
        $this->assertFalse($page->find('css', "#communicationModal")->isVisible());

        // Once the page has reloaded, assert that the new communication is listed on the page
        // Avoid doing this... Make it more specific.
        $this->assertNotNull($page->find('named', array('content', "Phone")));
        $this->assertNotNull($page->find('named', array('content', "Outgoing")));
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
        $this->session->visit('http://localhost:8000/app_test.php/property/1');
        // Get the page
        $page = $this->session->getPage();

        // find the new communication button
        $newCommunicationBtn = $page->find('css', "#newCommunication");
        // Click the new communication button
        $newCommunicationBtn->click();

        $this->session->wait(1000);

        // Assert that the new communication modal has appeared.
        $this->assertTrue($page->find('css', "#communicationModal")->isVisible());

        // Fill out the new communication form
        // Do not set type, this should make the form invalid
        $page->find('css',"#appbundle_communication_type")->setValue("0"); //The default value (which is INVALID)
        $page->find('css',"#appbundle_communication_medium_1")->click();
        $page->find('css',"#appbundle_communication_contactName")->setValue("Mr. Man");
        $page->find('css',"#appbundle_communication_contactEmail")->setValue("mr.man@manson.ca");
        $page->find('css',"#appbundle_communication_contactPhone")->setValue("123-456-7891");
        $page->find('css',"#appbundle_communication_category")->selectOption("Container");
        $page->find('css',"#appbundle_communication_description")->setValue("Mr. Man phoned and said there was a dune buggy stuck inside his recycling container. He wants it gone.");

        // Submit the form
        $page->find('css',"#communicationSubmit")->click();

        $this->session->wait(2000);

        // Once the page has reloaded, assert that the modal is still visible
        $this->assertTrue($page->find('css', "#communicationModal")->isVisible());

        // assert that there is an error message on the page
       // $this->assertNotNull($page->find('named', array('content', "Please select a type of communication")));
        $this->assertContains("Please select a type of communication",$page->find('css',"#appbundle_communication .ui.message")->getHtml());

    }

    /**
     * Story 11d
     * Tests that clicking on a row of a listed communication will take you to it's view page
     */
    public function testCommunicationClickView()
    {
        // Navigate to the property view page
        $this->session->visit('http://localhost:8000/app_test.php/property/17');
        // Get the page
        $page = $this->session->getPage();

        $this->session->wait(8000);

        // Click the desired communication (id 2 in this case)
        $page->find('css', '#tblCommunications tbody tr')->find('named', array('content', "Ken"))->click();

        $this->session->wait(5000);

        // Assert that we're on the right page
        $this->assertContains('/communication/2', $this->session->getCurrentUrl());

        // Assert information from that page
        //$this->assertNotNull($page->find('named', array('content', "2")));
        $this->assertNotNull($page->find('named', array('content', "Ken")));
    }


    /**
     * Story 11d
     * Tests that clicking on a row of a listed container will take you to it's view page
     */
    public function testContainerClickView()
    {
        // Navigate to the property view page
        $this->session->visit('http://localhost:8000/app_test.php/property/20');
        // Get the page
        $page = $this->session->getPage();

        // Click the desired contact (serial 888888)
        $page->find('css', 'table.containers')->find('named', array('content', "888888"))->click();

        // Assert that we're on the right page
        $this->assertContains('/container/2', $this->session->getCurrentUrl());

        // Assert information from that page
        $this->assertNotNull($page->find('named', array('content', "888888")));
    }

    /**
     * Story 11d
     * Tests that you can browse to the communication edit page for an existing communication
     */
    public function testCommunicationEditBrowse()
    {
        // Navigate to the home page
        $this->session->visit('http://localhost:8000/app_test.php/');

        // Get the page
        $page = $this->session->getPage();


        //browse to the property search page
        $page->find('css',"#propertiesPage")->click();

        //$this->session->wait(3000);

        //get the searchbox
        $searchbox = $page->find('css','#searchBox');
        //Type into the searchbox
        $searchbox->setValue("123 Fake Street");
        $searchbox->keyPress('s');

        $this->session->wait(3000);

        //click the first row
        $page->find("css",".ui.table tbody tr:first-child")->click();

        //click on the first communication
        $communicationRow = $page->find('css','.communications.ui.table tbody tr:first-child');

        $communicationRow->click();

        //click the edit button
        $page->find('named',array('content','Edit'))->click();

        //check that the header contains edit communication
        $this->assertContains('Edit Communication', $page->find('css','h2')->getHtml());

        //the communication has the same contact email in the field (just a field to check that they're the same one)
        $this->assertContains('email@email.com', $page->find('css','#appbundle_communication_contactEmail')->getValue());
    }

    public function testCommunicationEditSuccess()
    {
        // Navigate to the edit page
        $this->session->visit('http://localhost:8000/app_test.php/communication/2/edit');

        // Get the page
        $page = $this->session->getPage();

        //edit all the fields
        $page->find("css","#appbundle_communication_type")->selectOption('Email');
        $page->find("css","#appbundle_communication_medium_1")->click();
        $page->find("css","#appbundle_communication_contactName")->setValue("Adam");
        $page->find("css","#appbundle_communication_contactEmail")->setValue('Adam@cosmo.com');
        $page->find("css","#appbundle_communication_contactPhone")->setValue("222-222-2222");
        $page->find("css","#appbundle_communication_property")->selectOption('123 Fake St');
        $page->find("css","#appbundle_communication_category")->selectOption("Misc");
        $page->find("css","#appbundle_communication_description")->setValue("Some goof put a dune buggy in the recycling bin!");

        //submit the form
        $page->find("css","#btnSave")->click();

        //check that we're on the view communication page
        $this->assertContains('View Communication',$page->find("css","h2")->getHtml());


        //Check that all the fields have changed
        $this->assertNotNull($page->find("named",array("content","Email")));
        $this->assertNotNull($page->find("named",array("content","Outgoing")));
        $this->assertNotNull($page->find("named",array("content","Adam")));
        $this->assertNotNull($page->find("named",array("content","Adam@cosmo.com")));
        $this->assertNotNull($page->find("named",array("content","222-222-2222")));
        $this->assertNotNull($page->find("named",array("content","123 Fake St")));
        $this->assertNotNull($page->find("named",array("content","Misc")));
        $this->assertNotNull($page->find("named",array("content","Some goof put a dune buggy in the recycling bin!")));


    }

    public function testCommunicationEditFailure()
    {
        // Navigate to the edit page
        $this->session->visit('http://localhost:8000/app_test.php/communication/2/edit');

        // Get the page
        $page = $this->session->getPage();

        // Disable browser validation
        $this->session->executeScript('javascript:for(var f=document.forms,i=f.length;i--;)f[i].setAttribute("novalidate",i)');

        // make things super invalid
        $page->find("css","#appbundle_communication_type")->selectOption('...');
        $page->find("css","#appbundle_communication_contactName")->setValue(str_repeat("8", 300));
        $page->find("css","#appbundle_communication_contactEmail")->setValue('Adam.cosmo@com');
        $page->find("css","#appbundle_communication_contactPhone")->setValue("2322-222 2222");
        $page->find("css","#appbundle_communication_category")->selectOption("...");
        $page->find("css","#appbundle_communication_description")->setValue("");

        //submit the form
        $page->find("css","#btnSave")->click();

        //Check that a bunch of errors showed up
        $this->assertNotNull($page->find("named",array("content","Please select a type of communication")));
        $this->assertNotNull($page->find("named",array("content","Email must be in the format of 'Example@example.com'")));
        $this->assertNotNull($page->find("named",array("content","Contact name must be less than 255 characters")));
        $this->assertNotNull($page->find("named",array("content","Phone number must be in the format of ###-###-####")));
        $this->assertNotNull($page->find("named",array("content","Please select a category")));
        $this->assertNotNull($page->find("named",array("content","Please provide a brief description of the communication")));
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
        $stmt = $this->em->getConnection()->prepare('DELETE FROM Container');
        $stmt->execute();
        $this->em->close();
        $this->em = null; //avoid memory meaks
    }
}