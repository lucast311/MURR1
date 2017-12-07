<?php
<<<<<<< HEAD

namespace Tests\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ContactControllerTest extends WebTestCase
{
=======
namespace Tests\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use AppBundle\Entity\Contact;
use AppBundle\Services\Changer;
use AppBundle\Services\SearchNarrower;
use AppBundle\DataFixtures\ORM\LoadContactData;

class ContactControllerTest extends WebTestCase
{
    private $em;

    protected function setUp()
    {
        self::bootKernel();
        $this->em = static::$kernel->getContainer()
            ->get('doctrine')
            ->getManager();

        $contactLoader = new LoadContactData();
        $contactLoader->load($this->em);
    }


>>>>>>> master
    /**
     * Story 9a
     * Tests the list action. Ensures that a table exists in the html with the right headers.
     */
    public function testListAction()
    {
        //Create a client to go through the web page
        $client = static::createClient();
        //Request the contact add page
        $crawler = $client->request('GET','/contact/list');

        // Assert that a table exists
        $this->assertGreaterThan(0, $crawler->filter('table')->count());
        // Assert that the table has the proper headings
        $this->assertGreaterThan(0, $crawler->filter('html:contains("First Name")')->count());
        $this->assertGreaterThan(0, $crawler->filter('html:contains("Last Name")')->count());
        $this->assertGreaterThan(0, $crawler->filter('html:contains("Organization")')->count());
        $this->assertGreaterThan(0, $crawler->filter('html:contains("Primary Phone")')->count());
    }

    /**
     * Story 9a
     * Tests the viewing of a specific contact. Ensures that the page can be navigated to through the list
     * and that it contains all the required labels on the page.
     */
    public function testViewAction()
    {
        //Create a client to go through the web page
        $client = static::createClient();
        //Request the contact add page
        $crawler = $client->request('GET','/contact/list');
        // Select the first button on the page that views the details for a contact
        $link = $crawler->filter('a:contains("View")')->eq(0)->link();
        // Go there - should be viewing a specific contact after this
        $crawler = $client->click($link);

        // Assert that all the proper labels are on the page
        $this->assertGreaterThan(0, $crawler->filter('html:contains("First Name:")')->count());
        $this->assertGreaterThan(0, $crawler->filter('html:contains("Last Name:")')->count());
        $this->assertGreaterThan(0, $crawler->filter('html:contains("Organization:")')->count());
        $this->assertGreaterThan(0, $crawler->filter('html:contains("Primary Phone:")')->count());
        $this->assertGreaterThan(0, $crawler->filter('html:contains("Secondary Phone:")')->count());
        $this->assertGreaterThan(0, $crawler->filter('html:contains("Phone Extension:")')->count());
        $this->assertGreaterThan(0, $crawler->filter('html:contains("Email Address:")')->count());
        $this->assertGreaterThan(0, $crawler->filter('html:contains("Fax:")')->count());
        $this->assertGreaterThan(0, $crawler->filter('html:contains("Street Address:")')->count());
        $this->assertGreaterThan(0, $crawler->filter('html:contains("Postal Code:")')->count());
        $this->assertGreaterThan(0, $crawler->filter('html:contains("City:")')->count());
        $this->assertGreaterThan(0, $crawler->filter('html:contains("Province:")')->count());
        $this->assertGreaterThan(0, $crawler->filter('html:contains("Country:")')->count());
    }

<<<<<<< HEAD
    // story 9c tests
    public function testEditRedirect()
    {
        //Create a client to go through the web page
        $client = static::createClient();
        //Request the contact edit page
        $crawler = $client->request('GET','/contact/');
        // Select the first button on the page that views the details for a contact
        $link = $crawler->filter('a[href="/contact/1/edit"]')->eq(0)->link();
        // Go there - should be viewing a specific contact after this
        $crawler = $client->click($link);

        $this->assertGreaterThan(0, $crawler->filter($("h1:contains(Contact Edit)")->count());
    }

    public function testEditSubmitRedirect()
    {
        //Create a client to go through the web page
        $client = static::createClient();
        //Request the contact edit page
        $crawler = $client->request('GET','/contact/list/1/edit');
        // Select the first button on the page that views the details for a contact
        $link = $crawler->filter('a:contains("Edit")')->eq(0)->link();
        // Go there - should be viewing a specific contact after this
        $crawler = $client->click($link);

        $this->assertGreaterThan(0, $crawler->filter('html:contains("View contact")')->count());
    }
}

=======
    public function testAddActionSuccess()
    {
        //Create a client to go through the web page
        $client = static::createClient();
        //Reques the contact add page
        $crawler = $client->request('GET','/contact/add');
        //select the form and add values to it.
        $form = $crawler->selectButton('Save')->form();
        $form['contact[firstName]'] = 'Bob';
        $form['contact[lastName]'] = 'frank';
        $form['contact[organization]'] = 'Murr';
        $form['contact[primaryPhone]'] = '306-921-3344';
        $form['contact[phoneExtention]'] = '';
        $form['contact[secondaryPhone]'] = '';
        $form['contact[emailAddress]'] = 'murr123@gmail.com';
        $form['contact[fax]'] = '';
        $form['contact[address][streetAddress]'] = '123 Main Street';
        $form['contact[address][postalCode]'] = 'S7N 0R7';
        $form['contact[address][city]'] = 'Saskatoon';
        $form['contact[address][province]'] = 'Saskatchewan';
        $form['contact[address][country]'] = 'Canada';
        //crawler submits the form
        $crawler = $client->submit($form);
        //check for the success message
        $this->assertGreaterThan(
            0,
            $crawler->filter('html:contains("Contact has been successfully added")')->count()
            );


    }


    public function testAddActionFailurefName()
    {
        //Create a client to go through the web page
        $client = static::createClient();
        //Reques the contact add page
        $crawler = $client->request('GET','/contact/add');
        //select the form and add values to it.
        $form = $crawler->selectButton('Save')->form();
        $form['contact[firstName]'] = '';
        $form['contact[lastName]'] = 'frank';
        $form['contact[organization]'] = 'Murr';
        $form['contact[primaryPhone]'] = '306-921-3344';
        $form['contact[phoneExtention]'] = '';
        $form['contact[secondaryPhone]'] = '';
        $form['contact[emailAddress]'] = 'murr123@gmail.com';
        $form['contact[fax]'] = '';
        $form['contact[address][streetAddress]'] = '123 Main Street';
        $form['contact[address][postalCode]'] = 'S7N 0R7';
        $form['contact[address][city]'] = 'Saskatoon';
        $form['contact[address][province]'] = 'Saskatchewan';
        $form['contact[address][country]'] = 'Canada';
        //crawler submits the form
        $crawler = $client->submit($form);
        //check for the success message
        $this->assertGreaterThan(
            0,
            $crawler->filter('html:contains("First name cannot be left blank")')->count()
            );


    }

    public function testAddActionFailurelName()
    {
        //Create a client to go through the web page
        $client = static::createClient();
        //Reques the contact add page
        $crawler = $client->request('GET','/contact/add');
        //select the form and add values to it.
        $form = $crawler->selectButton('Save')->form();
        $form['contact[firstName]'] = 'Bob';
        $form['contact[lastName]'] = '';
        $form['contact[organization]'] = 'Murr';
        $form['contact[primaryPhone]'] = '306-921-3344';
        $form['contact[phoneExtention]'] = '';
        $form['contact[secondaryPhone]'] = '';
        $form['contact[emailAddress]'] = 'murr123@gmail.com';
        $form['contact[fax]'] = '';
        $form['contact[address][streetAddress]'] = '123 Main Street';
        $form['contact[address][postalCode]'] = 'S7N 0R7';
        $form['contact[address][city]'] = 'Saskatoon';
        $form['contact[address][province]'] = 'Saskatchewan';
        $form['contact[address][country]'] = 'Canada';
        //crawler submits the form
        $crawler = $client->submit($form);
        //check for the success message
        $this->assertGreaterThan(
            0,
            $crawler->filter('html:contains("Last name cannot be left blank")')->count()
            );


    }

    public function testAddActionFailureEmailAddress()
    {
        //Create a client to go through the web page
        $client = static::createClient();
        //Reques the contact add page
        $crawler = $client->request('GET','/contact/add');
        //select the form and add values to it.
        $form = $crawler->selectButton('Save')->form();
        $form['contact[firstName]'] = 'Bob';
        $form['contact[lastName]'] = 'Frank';
        $form['contact[organization]'] = 'Murr';
        $form['contact[primaryPhone]'] = '306-921-3344';
        $form['contact[phoneExtention]'] = '';
        $form['contact[secondaryPhone]'] = '';
        $form['contact[emailAddress]'] = '';
        $form['contact[fax]'] = '';
        $form['contact[address][streetAddress]'] = '123 Main Street';
        $form['contact[address][postalCode]'] = 'S7N 0R7';
        $form['contact[address][city]'] = 'Saskatoon';
        $form['contact[address][province]'] = 'Saskatchewan';
        $form['contact[address][country]'] = 'Canada';
        //crawler submits the form
        $crawler = $client->submit($form);
        //check for the success message
        $this->assertGreaterThan(
            0,
            $crawler->filter('html:contains("Email address cannot be left blank")')->count()
            );


    }

    public function testAddActionFailurePostalCode()
    {
        //Create a client to go through the web page
        $client = static::createClient();
        //Reques the contact add page
        $crawler = $client->request('GET','/contact/add');
        //select the form and add values to it.
        $form = $crawler->selectButton('Save')->form();
        $form['contact[firstName]'] = 'Bob';
        $form['contact[lastName]'] = 'Frank';
        $form['contact[organization]'] = 'Murr';
        $form['contact[primaryPhone]'] = '306-921-3344';
        $form['contact[phoneExtention]'] = '';
        $form['contact[secondaryPhone]'] = '';
        $form['contact[emailAddress]'] = 'murr123@gmail.com';
        $form['contact[fax]'] = '';
        $form['contact[address][streetAddress]'] = '123 Main Street';
        $form['contact[address][postalCode]'] = '';
        $form['contact[address][city]'] = 'Saskatoon';
        $form['contact[address][province]'] = 'Saskatchewan';
        $form['contact[address][country]'] = 'Canada';
        //crawler submits the form
        $crawler = $client->submit($form);
        //check for the success message
        $this->assertGreaterThan(
            0,
            $crawler->filter('html:contains("Postal code cannot be left blank")')->count()
            );


    }


    public function testAddActionFailureProvince()
    {
        //Create a client to go through the web page
        $client = static::createClient();
        //Reques the contact add page
        $crawler = $client->request('GET','/contact/add');
        //select the form and add values to it.
        $form = $crawler->selectButton('Save')->form();
        $form['contact[firstName]'] = 'Bob';
        $form['contact[lastName]'] = 'Frank';
        $form['contact[organization]'] = 'Murr';
        $form['contact[primaryPhone]'] = '306-921-3344';
        $form['contact[phoneExtention]'] = '';
        $form['contact[secondaryPhone]'] = '';
        $form['contact[emailAddress]'] = 'murr123@gmail.com';
        $form['contact[fax]'] = '';
        $form['contact[address][streetAddress]'] = '123 Main Street';
        $form['contact[address][postalCode]'] = 'S7N 0R7';
        $form['contact[address][city]'] = 'Saskatoon';
        $form['contact[address][province]'] = '';
        $form['contact[address][country]'] = 'Canada';
        //crawler submits the form
        $crawler = $client->submit($form);
        //check for the success message
        $this->assertGreaterThan(
            0,
            $crawler->filter('html:contains("Province cannot be left blank")')->count()
            );


    }


    public function testAddActionFailureCountry()
    {
        //Create a client to go through the web page
        $client = static::createClient();
        //Reques the contact add page
        $crawler = $client->request('GET','/contact/add');
        //select the form and add values to it.
        $form = $crawler->selectButton('Save')->form();
        $form['contact[firstName]'] = 'Bob';
        $form['contact[lastName]'] = 'Frank';
        $form['contact[organization]'] = 'Murr';
        $form['contact[primaryPhone]'] = '306-921-3344';
        $form['contact[phoneExtention]'] = '';
        $form['contact[secondaryPhone]'] = '';
        $form['contact[emailAddress]'] = 'murr123@gmail.com';
        $form['contact[fax]'] = '';
        $form['contact[address][streetAddress]'] = '123 Main Street';
        $form['contact[address][postalCode]'] = 'S7N 0R7';
        $form['contact[address][city]'] = 'Saskatoon';
        $form['contact[address][province]'] = 'Saskatchewan';
        $form['contact[address][country]'] = '';
        //crawler submits the form
        $crawler = $client->submit($form);
        //check for the success message
        $this->assertGreaterThan(
            0,
            $crawler->filter('html:contains("Country cannot be left blank")')->count()
            );
    }



    /////////////////////////////////////////////////////


    /**
     * test that the query successfully returns records in JSON format
     */
    public function testSuccessfullyReceiveSearch()
    {
        // get a repository so we can query for data
        $repository = $this->em->getRepository(Contact::class);

        // create a client so we can view the page
        $client = static::createClient();

        // go to the page and search for 'Jim'
        $client->request('GET', '/contact/search/Jim');

        // create an array so we can call the search
        $queryStrings = array();
        $queryStrings[] = 'Jim';

        // query the database
        $repository->contactSearch($queryStrings);

        // assert that what we expect is actually returned
        $this->assertContains('[{&quot;id&quot;:152,&quot;firstName&quot;:&quot;Jim&quot;,&quot;lastName&quot;:&quot;Jim&quot;,&quot;organization&quot;:null,&quot;primaryPhone&quot;:&quot;969-555-6969&quot;,&quot;phoneExtention&quot;:&quot;123&quot;,&quot;secondaryPhone&quot;:null,&quot;emailAddress&quot;:&quot;tmctest@testcorp.com&quot;,&quot;fax&quot;:null,&quot;address&quot;:152}]', $client->getResponse()->getContent());
    }

    /**
     * test that the query to search on is too long
     */
    public function testQueryTooLong()
    {
        //// get a repository so we can query for data
        //$repository = $this->em->getRepository(Contact::class);

        // create a client so we can view the page
        $client = static::createClient();

        // go to the page and search for 'BobJonesBobJonesBobJonesBobJonesBobJonesBobJonesBobJonesBobJonesBobJonesBobJonesBobJonesBobJonesBobJonesBobJonesBobJonesBobJonesBobJonesBobJonesBobJonesBobJonesBobJonesBobJonesBobJonesBobJonesBobJonesBobJonesBobJonesBobJonesBobJones'
        $client->request('GET', '/contact/search/BobJonesBobJonesBobJonesBobJonesBobJonesBobJonesBobJonesBobJonesBobJonesBobJonesBobJonesBobJonesBobJonesBobJonesBobJonesBobJonesBobJonesBobJonesBobJonesBobJonesBobJonesBobJonesBobJonesBobJonesBobJonesBobJonesBobJonesBobJonesBobJones');

        //// query the database
        //$repository->contactSearch("Jim");

        // assert that what we expect is actually returned
        $this->assertContains('[{&quot;role&quot;:null}]', $client->getResponse()->getContent());
    }

    /**
     * test that the Changer actually converts Entities into JSON string objects
     */
    public function testChangerFunctionality()
    {
        // create new Changer and SearchNarrower objects that will be used later
        $changer = new Changer();
        $searchNarrower = new SearchNarrower();

        // get a repository so we can query for data
        $repository = $this->em->getRepository(Contact::class);

        // create a client so we can view the page
        $client = static::createClient();

        // go to the page and search for 'Jim'
        $client->request('GET', '/contact/search/Jim');

        // query the database
        $results = $repository->contactSearch("Jim");

        // create an array so we can narrow the records
        $cleanQuery = array();
        $cleanQuery[] = 'Bob';
        $cleanQuery[] = 'Jones';

        // narrow the results
        $narrowedSearches = $searchNarrower->narrowContacts($results, $cleanQuery);

        // convert to JSON string
        $jsonFormat = $changer->ToJSON($results[0], $narrowedSearches[1][1]);

        // Assert that the format that the search returns, is not the same as format returned by the Changer
        $this->assertTrue($results != $jsonFormat);
    }



    /////////////////////////////////////////////////////


    /**
     * (@inheritDoc)
     */
    protected function tearDown()
    {
        parent::tearDown();

        $stmt = $this->em->getConnection()->prepare("DELETE FROM Contact");
        $stmt->execute();
        $stmt = $this->em->getConnection()->prepare("DELETE FROM Address");
        $stmt->execute();

        $this->em->close();
        $this->em = null;//avoid memory meaks
    }
}
>>>>>>> master
