<?php
require_once 'vendor/autoload.php';
use DMore\ChromeDriver\ChromeDriver;
use Behat\Mink\Session;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use AppBundle\DataFixtures\ORM\LoadUserData;

use AppBundle\Entity\Property;
use AppBundle\Entity\Contact;
use Doctrine\Common\Collections\ArrayCollection;
/**
 * PropertyContactRemoveTest short summary.
 *
 * PropertyContactRemoveTest description.
 *
 * @version 1.0
 * @author cst201
 */
class PropertyContactRemoveTest extends WebTestCase
{
    private $driver;
    private $session;
    private $em;

    protected function setUp()
    {
        self::bootKernel();

        $this->em = static::$kernel->getContainer()
            ->get('doctrine')
            ->getManager();

        //Wipe database before beginning because tests seem to run into errors
        $stmt = $this->em->getConnection()->prepare('DELETE FROM Property');
        $stmt->execute();
        $stmt = $this->em->getConnection()->prepare('DELETE FROM Contact');
        $stmt->execute();
        $stmt = $this->em->getConnection()->prepare('DELETE FROM User');
        $stmt->execute();

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
        $this->session->visit('http://localhost:8000/login');
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
     * Story 4L
     * Tests that when you click the remove button, it shows the other two buttons instead
     */
    public function testRemoveButtonShowsOtherButtons()
    {
        //create a new property
        $property = new Property();

        $repo = $this->em->getRepository(Property::class);
        $repo->save($property);

        //create a new contact
        $contact = new Contact();
        $contact->setFirstName("Testman");
        $contact->setRole("Owner");

        //associate the two
        $arrayCollection = new ArrayCollection();
        $arrayCollection->add($contact);
        $property->setContacts($arrayCollection);
        //now that the data exists, go to the page
        //start up a new session
        $this->session->visit('http:://localhost:8000/property/1');
        //get the page
        $page = $this->session->getPage();
        //find the button with the ID of the remove button
        assertNotNull($page->find("css", "#rmb1"));

        //click on the button
        $removeButton = $page->find("css", "#rmb1");
        $removeButton->click();

        //test that that button no longer exists, but the other two do
        assertNull($page->find("css","#rmb1"));
        assertNotNull($page->find("css", "#rmba1"));
        assertNotNull($page->find("css","#rmbc1"));

    }

    /**
     * Story 4L
     * Tests that a user can remove a contact from a property
     */
    public function testRemoveContactFromPropertyAccept()
    {
        //create a new property
        $property = new Property();

        $repo = $this->em->getRepository(Property::class);
        $repo->save($property);

        //create a new contact
        $contact = new Contact();
        $contact->setFirstName("Testman");
        $contact->setRole("Owner");

        //associate the two
        $arrayCollection = new ArrayCollection();
        $arrayCollection->add($contact);
        $property->setContacts($arrayCollection);
        //now that the data exists, go to the page
        //start up a new session
        $this->session->visit('http:://localhost:8000/property/1');
        //get the page
        $page = $this->session->getPage();

        //click on the button
        $removeButton = $page->find("css", "#rmb1");
        $removeButton->click();

        //ensure the message exists
        $this->assertContains("Are you sure you want to remove association?", $page->getHtml());

        //click the checkmark
        $acceptButton = $page->find('css','#rmba1');
        $acceptButton->click();

        //ensure the name does not load on the page again
        $this->assertNotContains("Testman", $page->getHtml());
    }

    /**
     * Story 4L
     * Tests that a user can cancel removing a contact from a property
     */
    public function testRemoveContactFromPropertyCancel()
    {
        //create a new property
        $property = new Property();

        $repo = $this->em->getRepository(Property::class);
        $repo->save($property);

        //create a new contact
        $contact = new Contact();
        $contact->setFirstName("Testman");
        $contact->setRole("Owner");

        //associate the two
        $arrayCollection = new ArrayCollection();
        $arrayCollection->add($contact);
        $property->setContacts($arrayCollection);
        //now that the data exists, go to the page
        //start up a new session
        $this->session->visit('http:://localhost:8000/property/1');
        //get the page
        $page = $this->session->getPage();

        //click on the button
        $removeButton = $page->find("css", "#rmb1");
        $removeButton->click();

        //click the cancel button
        $cancelBtn = $page->find("css","#rmbc1");
        $cancelBtn->click();

        //ensure the two other buttons aren't on the page
        $this->assertNull($page->find("css","#rmbc1"));
        $this->assertNull($page->find("css","#rmba1"));

        //ensure the remove button is back
        $this->assertNotNull($page->find("css", "#rmb1"));
    }

    protected function tearDown()
    {
        parent::tearDown();
        // After the test has been run, make sure to restart the session so you don't run into problems
        $this->session->stop();

        //Now wipe the database
        $stmt = $this->em->getConnection()->prepare('DELETE FROM Property');
        $stmt->execute();
        $stmt = $this->em->getConnection()->prepare('DELETE FROM Contact');
        $stmt->execute();
        $stmt = $this->em->getConnection()->prepare('DELETE FROM User');
        $stmt->execute();
    }
}