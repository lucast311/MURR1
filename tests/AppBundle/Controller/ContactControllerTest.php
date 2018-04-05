<?php

namespace Tests\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use AppBundle\Entity\Contact;
//use AppBundle\Entity\ContactProperty;
use AppBundle\Services\Changer;
use AppBundle\Services\SearchNarrower;
use AppBundle\DataFixtures\ORM\LoadContactData;
use AppBundle\Entity\Address;
use AppBundle\Entity\Property;
use AppBundle\DataFixtures\ORM\LoadUserData;
use Doctrine\Common\Collections\ArrayCollection;
use Tests\AppBundle\DatabasePrimer;

class ContactControllerTest extends WebTestCase
{
    private $em;

    public static function setUpBeforeClass()
    {
        self::bootKernel();
        DatabasePrimer::prime(self::$kernel);
    }


    /**
     * (@inheritDoc)
     */
    protected function setUp()
    {
        self::bootKernel();

        //self::bootKernel();
        //$this->em = static::$kernel->getContainer()
        //    ->get('doctrine')
        //    ->getManager();



        $this->em = static::$kernel->getContainer()
            ->get('doctrine')
            ->getManager();

        $contactLoader = new LoadContactData();
        $contactLoader->load($this->em);

        // Load the admin user into the database so they can log in
        $encoder = static::$kernel->getContainer()->get('security.password_encoder');

        $userLoader = new LoadUserData($encoder);
        $userLoader->load($this->em);
    }

    /**
     * Story 9a
     * Tests the list action. Ensures that a table exists in the html with the right headers.
     */
    public function testListAction()
    {
        //Create a client to go through the web page
        $client = static::createClient(array(), array('PHP_AUTH_USER' => 'admin', 'PHP_AUTH_PW'   => 'password'));
        $client->followRedirects(true);
        //Request the contact add page
        $crawler = $client->request('GET','/contact/');

        //$this->assertGreaterThan(0, $crawler->filter('table')->count());

        //$this->assertGreaterThan(0, $crawler->filter('html:contains("First Name")')->count());
        //$this->assertGreaterThan(0, $crawler->filter('html:contains("Last Name")')->count());
        //$this->assertGreaterThan(0, $crawler->filter('html:contains("Company Name")')->count());
        //$this->assertGreaterThan(0, $crawler->filter('html:contains("Primary Phone")')->count());
        $response = $client->getResponse()->getContent();



        $this->assertcontains("First Name", $response);
        $this->assertcontains("Last Name", $response);
        $this->assertcontains("Company Name", $response);
        $this->assertcontains("Primary Phone", $response);
    }

    /**
     * Story 9a
     * Tests the viewing of a specific contact. Ensures that the page can be navigated to through the list
     * and that it contains all the required labels on the page.
     */
    public function testViewAction()
    {
        //create a contact to insert
        $contact = new Contact();
        $contact->setFirstName("Ashton");
        $contact->setLastName("South");
        $contact->setCompanyName("COSMO!");
        $contact->setRole("Property Manager");
        $contact->setprimaryPhone("306-345-8932");
        $contact->setEmailAddress("south@gmail.com");

        //create an address to add for the contact
        $address = new Address();
        $address->setStreetAddress("123 Main Street");
        $address->setPostalCode("S7N 3K5");
        $address->setCity("Saskatoon");
        $address->setProvince("Saskatchewan");
        $address->setCountry("Canada");

        $contact->setAddress($address);

        $repository = $this->em->getRepository(Contact::class);
         //save contact to database
        $id = $repository->save($contact);

        //Create a client to go through the web page
        $client = static::createClient(array(), array('PHP_AUTH_USER' => 'admin', 'PHP_AUTH_PW'   => 'password'));
        //Request the contact view contact page for this contact
        $crawler = $client->request('GET',"/contact/$id");

        //// Select the first button on the page that views the details for a contact
        //$link = $crawler->filter('a:contains("View")')->eq(0)->link();
        //// Go there - should be viewing a specific contact after this
        //$crawler = $client->click($link);

        // Assert that all the proper labels are on the page
        $this->assertGreaterThan(0, $crawler->filter('html:contains("First Name:")')->count());
        $this->assertGreaterThan(0, $crawler->filter('html:contains("Last Name:")')->count());
        $this->assertGreaterThan(0, $crawler->filter('html:contains("Role:")')->count());
        $this->assertGreaterThan(0, $crawler->filter('html:contains("Primary Phone:")')->count());
        $this->assertGreaterThan(0, $crawler->filter('html:contains("Secondary Phone:")')->count());
        $this->assertGreaterThan(0, $crawler->filter('html:contains("Phone Extension:")')->count());
        $this->assertGreaterThan(0, $crawler->filter('html:contains("Email Address:")')->count());
        $this->assertGreaterThan(0, $crawler->filter('html:contains("Fax:")')->count());
        //$this->assertGreaterThan(0, $crawler->filter('html:contains("Street Address:")')->count());
        //$this->assertGreaterThan(0, $crawler->filter('html:contains("Postal Code:")')->count());
        //$this->assertGreaterThan(0, $crawler->filter('html:contains("City:")')->count());
        //$this->assertGreaterThan(0, $crawler->filter('html:contains("Province:")')->count());
        //$this->assertGreaterThan(0, $crawler->filter('html:contains("Country:")')->count());
    }


    // story 9c tests
    public function testEditRedirect()
    {
        //Create a client to go through the web page
        $client = static::createClient(array(), array('PHP_AUTH_USER' => 'admin', 'PHP_AUTH_PW'   => 'password'));
        //Request the contact edit page
        $crawler = $client->request('GET','/contact/');
        // Select the first button on the page that views the details for a contact
        $link = $crawler->filter('a[href="/contact/1/edit"]')->eq(0)->link();
        // Go there - should be viewing a specific contact after this
        $crawler = $client->click($link);

        $this->assertGreaterThan(0, $crawler->filter(("#contentSeparator h2:contains(Contact Edit)"))->count());
    }

    public function testEditSubmitRedirect()
    {
        //Create a client to go through the web page
        $client = static::createClient(array(), array('PHP_AUTH_USER' => 'admin', 'PHP_AUTH_PW'   => 'password'));
        //Request the contact edit page
        $crawler = $client->request('GET','/contact/1/edit');
        //$link = $crawler->filter('a:contains("Edit")')->eq(0)->link();
        //// Go there - should be viewing a specific contact after this
        //$crawler = $client->click($link);

        $form = $crawler->selectButton('Save')->form();

        $crawler = $client->submit($form);

        $this->assertGreaterThan(0, $crawler->filter('html:contains("Contact")')->count());
    }



    public function testAddActionSuccess()
    {
        //Create a client to go through the web page
        $client = static::createClient(array(), array('PHP_AUTH_USER' => 'admin', 'PHP_AUTH_PW'   => 'password'));
        //Reques the contact add page
        $crawler = $client->request('GET','/contact/new');
        //select the form and add values to it.
        $form = $crawler->selectButton('Add')->form();
        $form['appbundle_contact[firstName]'] = 'Bob';
        $form['appbundle_contact[lastName]'] = 'frank';
        $form['appbundle_contact[role]'] = "Property Manager";
        $form['appbundle_contact[companyName]'] = 'Murr';
        $form['appbundle_contact[primaryPhone]'] = '306-921-3344';
        $form['appbundle_contact[phoneExtension]'] = '';
        $form['appbundle_contact[secondaryPhone]'] = '';
        $form['appbundle_contact[emailAddress]'] = 'murr123@gmail.com';
        $form["appbundle_contact[fax]"] = '';
        //crawler submits the form
        $crawler = $client->submit($form);
        //check for the success message
        //$this->assertGreaterThan(
        //    0,
        //    $crawler->filter('html:contains("Contact has been successfully added")')->count()
        //    );
        $this->assertContains('Redirecting to /contact', $client->getResponse()->getContent());


        $this->assertContains('Redirecting to /contact', $client->getResponse()->getContent());

    }

    /*
    public function testAddActionFailurefName()
    {
        //Create a client to go through the web page
        $client = static::createClient(array(), array('PHP_AUTH_USER' => 'admin', 'PHP_AUTH_PW'   => 'password'));
        //Reques the contact add page
        $crawler = $client->request('GET','/contact/add');
        //select the form and add values to it.
        $form = $crawler->selectButton('Save')->form();
        $form['contact[firstName]'] = '';
        $form['contact[lastName]'] = 'frank';
        $form['contact[organization]'] = 'Murr';
        $form['contact[primaryPhone]'] = '306-921-3344';
        $form['contact[phoneExtension]'] = '';
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
        $client = static::createClient(array(), array('PHP_AUTH_USER' => 'admin', 'PHP_AUTH_PW'   => 'password'));
        //Reques the contact add page
        $crawler = $client->request('GET','/contact/add');
        //select the form and add values to it.
        $form = $crawler->selectButton('Save')->form();
        $form['contact[firstName]'] = 'Bob';
        $form['contact[lastName]'] = '';
        $form['contact[organization]'] = 'Murr';
        $form['contact[primaryPhone]'] = '306-921-3344';
        $form['contact[phoneExtension]'] = '';
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
        $client = static::createClient(array(), array('PHP_AUTH_USER' => 'admin', 'PHP_AUTH_PW'   => 'password'));
        //Reques the contact add page
        $crawler = $client->request('GET','/contact/add');
        //select the form and add values to it.
        $form = $crawler->selectButton('Save')->form();
        $form['contact[firstName]'] = 'Bob';
        $form['contact[lastName]'] = 'Frank';
        $form['contact[organization]'] = 'Murr';
        $form['contact[primaryPhone]'] = '306-921-3344';
        $form['contact[phoneExtension]'] = '';
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
        $client = static::createClient(array(), array('PHP_AUTH_USER' => 'admin', 'PHP_AUTH_PW'   => 'password'));
        //Reques the contact add page
        $crawler = $client->request('GET','/contact/add');
        //select the form and add values to it.
        $form = $crawler->selectButton('Save')->form();
        $form['contact[firstName]'] = 'Bob';
        $form['contact[lastName]'] = 'Frank';
        $form['contact[organization]'] = 'Murr';
        $form['contact[primaryPhone]'] = '306-921-3344';
        $form['contact[phoneExtension]'] = '';
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
        $client = static::createClient(array(), array('PHP_AUTH_USER' => 'admin', 'PHP_AUTH_PW'   => 'password'));
        //Reques the contact add page
        $crawler = $client->request('GET','/contact/add');
        //select the form and add values to it.
        $form = $crawler->selectButton('Save')->form();
        $form['contact[firstName]'] = 'Bob';
        $form['contact[lastName]'] = 'Frank';
        $form['contact[organization]'] = 'Murr';
        $form['contact[primaryPhone]'] = '306-921-3344';
        $form['contact[phoneExtension]'] = '';
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
        $client = static::createClient(array(), array('PHP_AUTH_USER' => 'admin', 'PHP_AUTH_PW'   => 'password'));
        //Reques the contact add page
        $crawler = $client->request('GET','/contact/add');
        //select the form and add values to it.
        $form = $crawler->selectButton('Save')->form();
        $form['contact[firstName]'] = 'Bob';
        $form['contact[lastName]'] = 'Frank';
        $form['contact[organization]'] = 'Murr';
        $form['contact[primaryPhone]'] = '306-921-3344';
        $form['contact[phoneExtension]'] = '';
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
    /*





    /**
     * test that the query successfully returns records in JSON format
     */
    public function testSuccessfullyReceiveSearch()
    {
        // get a repository so we can query for data
        $repository = $this->em->getRepository(Contact::class);

        // create a client so we can view the page
        $client = static::createClient(array(), array('PHP_AUTH_USER' => 'admin', 'PHP_AUTH_PW'   => 'password'));

        // go to the page and search for 'Jim'
        $client->request('GET', '/contact/jsonsearch/Jim');

        // create an array so we can call the search
        $queryStrings = array();
        $queryStrings[] = 'Jim';

        // query the database
        $repository->contactSearch($queryStrings);

        // assert that what we expect is actually returned
        $this->assertContains('[{"id":22,"firstName":"Jim","lastName":"Jim","role":"Property Manager","primaryPhone":"969-555-6969","phoneExtension":123,"secondaryPhone":null,"emailAddress":"tmctest@testcorp.com","fax":null,"companyName":null}]', $client->getResponse()->getContent());
    }

    /**
     * test that the query to search on is too long
     */
    public function testQueryTooLong()
    {
        //// get a repository so we can query for data
        //$repository = $this->em->getRepository(Contact::class);

        // create a client so we can view the page
        $client = static::createClient(array(), array('PHP_AUTH_USER' => 'admin', 'PHP_AUTH_PW'   => 'password'));

        // go to the page and search for 'BobJonesBobJonesBobJonesBobJonesBobJonesBobJonesBobJonesBobJonesBobJonesBobJonesBobJonesBobJonesBobJonesBobJonesBobJonesBobJonesBobJonesBobJonesBobJonesBobJonesBobJonesBobJonesBobJonesBobJonesBobJonesBobJonesBobJonesBobJonesBobJones'
        $client->request('GET', '/contact/jsonsearch/BobJonesBobJonesBobJonesBobJonesBobJonesBobJonesBobJonesBobJonesBobJonesBobJonesBobJonesBobJonesBobJonesBobJonesBobJonesBobJonesBobJonesBobJonesBobJonesBobJonesBobJonesBobJonesBobJonesBobJonesBobJonesBobJonesBobJonesBobJonesBobJones');

        //// query the database
        //$repository->contactSearch("Jim");

        // assert that what we expect is actually returned
        $this->assertContains('[]', $client->getResponse()->getContent());
    }

    /**
     * test that the Changer actually converts Entities into JSON string objects
     */
    //public function testChangerFunctionality()
    //{
    //    // create new Changer and SearchNarrower objects that will be used later
    //    $changer = new Changer();
    //    $searchNarrower = new SearchNarrower();

    //    // get a repository so we can query for data
    //    $repository = $this->em->getRepository(Contact::class);

        // create a client so we can view the page
        //$client = static::createClient(array(), array('PHP_AUTH_USER' => 'admin', 'PHP_AUTH_PW'   => 'password'));

    //    // go to the page and search for 'Jim'
    //    $client->request('GET', '/contact/jsonsearch/Jim');

    //    // query the database
    //    $results = $repository->contactSearch("Jim");

    //    // create an array so we can narrow the records
    //    $cleanQuery = array();
    //    $cleanQuery[] = 'Bob';
    //    $cleanQuery[] = 'Jones';

    //    // narrow the results
    //    $narrowedSearches = $searchNarrower->narrower($results, $cleanQuery, new Contact());

    //    // convert to JSON string
    //    $jsonFormat = $changer->ToJSON($results[0], $narrowedSearches[1][1]);

    //    // Assert that the format that the search returns, is not the same as format returned by the Changer
    //    $this->assertTrue($results != $jsonFormat);
    //}


    /**
     * story 9i
     * test that the search page is accessable and that there is the proper elements on screen.
     */
    public function testSearchPageAccessible()
    {
        // Create a client, and go to the search page for a contact
        $client = static::createClient(array(), array('PHP_AUTH_USER' => 'admin', 'PHP_AUTH_PW'   => 'password'));

        // A crawler to check if the page contains a search field
        $crawler = $client->request('GET', '/contact/search');

        // Assert that the page contains both a Header, and a search field
        $this->assertContains("Contact Search", $client->getResponse()->getContent());
        $this->assertTrue($crawler->filter('input[type=search]')->first() != null);
    }

    /**
     * Story 9h
     * test that properties are associated and displayed
     */
    public function testViewPropertyAssociationSuccess()
    {
        //create a contact to insert
        $contact = new Contact();
        $contact->setFirstName("Ashton");
        $contact->setLastName("South");
        $contact->setCompanyName("COSMO!");
        $contact->setRole("Property Manager");
        $contact->setprimaryPhone("306-345-8932");
        $contact->setEmailAddress("south@gmail.com");

        //create an address to add for the contact
        $address = new Address();
        $address->setStreetAddress("123 Main Street");
        $address->setPostalCode("S7N 3K5");
        $address->setCity("Saskatoon");
        $address->setProvince("Saskatchewan");
        $address->setCountry("Canada");

        $contact->setAddress($address);

        //Create a new property to ensure that there is one to edit in the database
        $property = new Property();
        $property->setSiteId(7894854);
        $property->setPropertyName("Charlton Arms");
        $property->setPropertyType("Townhouse Condo");
        $property->setPropertyStatus("Active");
        $property->setNumUnits(5);
        $property->setNeighbourhoodName("Sutherland");
        $property->setNeighbourhoodId("O48");
        // Have to create a new valid address too otherwise doctrine will fail
        $address = new Address();
        $address->setStreetAddress("12 15th st east");
        $address->setPostalCode("S0E 1A0");
        $address->setCity("Saskatoon");
        $address->setProvince("Saskatchewan");
        $address->setCountry("Canada");
        $property->setAddress($address);
        $property->setContacts(new ArrayCollection(array($contact)));

        $propertyRepository = $this->em->getRepository(Property::class);
        //save contact to database
        $propertyRepository->save($property);

        //add the property to the contact
        $contact->setProperties(new ArrayCollection(array($property)));

        $contactRepository = $this->em->getRepository(Contact::class);
        //save contact to database
        $contactId = $contactRepository->save($contact);

        // Create a client,
        $client = static::createClient(array(), array('PHP_AUTH_USER' => 'admin', 'PHP_AUTH_PW'   => 'password'));

        // A crawler to check if the page contains a search field
        $crawler = $client->request('GET', "/contact/$contactId");

        //check if the headings appear
        $this->assertGreaterThan(0, $crawler->filter('html:contains("Site Id")')->count());
        $this->assertGreaterThan(0, $crawler->filter('html:contains("Street Address")')->count());
        $this->assertGreaterThan(0, $crawler->filter('html:contains("Neighbourhood")')->count());
        $this->assertGreaterThan(0, $crawler->filter('html:contains("# of Units")')->count());
        $this->assertGreaterThan(0, $crawler->filter('html:contains("Type")')->count());

        //assert that the associated data shows up
        $this->assertGreaterThan(0, $crawler->filter('html:contains("7894854")')->count());
        $this->assertGreaterThan(0, $crawler->filter('html:contains("12 15th st east")')->count());
        $this->assertGreaterThan(0, $crawler->filter('html:contains("Sutherland")')->count());
        $this->assertGreaterThan(0, $crawler->filter('html:contains("5")')->count());
        $this->assertGreaterThan(0, $crawler->filter('html:contains("Townhouse Condo")')->count());
    }

    /**
     * Story 9h
     * test that there are no associated properties, and the proper message is displayed
     */
    public function testViewPropertyNoAssociations()
    {
        //create a contact to insert
        $contact = new Contact();
        $contact->setFirstName("Ashton");
        $contact->setLastName("South");
        $contact->setCompanyName("COSMO!");
        $contact->setRole("Property Manager");
        $contact->setprimaryPhone("306-345-8932");
        $contact->setEmailAddress("south@gmail.com");

        //create an address to add for the contact
        $address = new Address();
        $address->setStreetAddress("123 Main Street");
        $address->setPostalCode("S7N 3K5");
        $address->setCity("Saskatoon");
        $address->setProvince("Saskatchewan");
        $address->setCountry("Canada");

        $contact->setAddress($address);

        $repository = $this->em->getRepository(Contact::class);
        //save contact to database
        $id = $repository->save($contact);

        // Create a client,
        $client = static::createClient(array(), array('PHP_AUTH_USER' => 'admin', 'PHP_AUTH_PW'   => 'password'));

        // A crawler to check if the page contains a search field
        $crawler = $client->request('GET', "/contact/$id");

        //check if the headings appear
        $this->assertGreaterThan(0, $crawler->filter('html:contains("No associated properties")')->count());
    }

    /**
     * Story 9h
     * test that properties are associated and displayed
     */
    public function testViewPropertyMultipleAssociationSuccess()
    {
        //create a contact to insert
        $contact = new Contact();
        $contact->setFirstName("Ashton");
        $contact->setLastName("South");
        $contact->setCompanyName("COSMO!");
        $contact->setRole("Property Manager");
        $contact->setprimaryPhone("306-345-8932");
        $contact->setEmailAddress("south@gmail.com");

        //create an address to add for the contact
        $address = new Address();
        $address->setStreetAddress("123 Main Street");
        $address->setPostalCode("S7N 3K5");
        $address->setCity("Saskatoon");
        $address->setProvince("Saskatchewan");
        $address->setCountry("Canada");

        $contact->setAddress($address);

        // Create the property 15 times
        $propertiesArray = array();

        for ($i = 0; $i < 15; $i++)
        {
            //Create a new property to ensure that there is one to edit in the database
            $property = new Property();
            $property->setSiteId($i);
            $property->setPropertyName("Charlton Arms");
            $property->setPropertyType("Townhouse Condo");
            $property->setPropertyStatus("Active");
            $property->setNumUnits(5);
            $property->setNeighbourhoodName("Sutherland");
            $property->setNeighbourhoodId("O48");
            // Have to create a new valid address too otherwise doctrine will fail
            $address = new Address();
            $address->setStreetAddress("12 15th st east");
            $address->setPostalCode("S0E 1A0");
            $address->setCity("Saskatoon");
            $address->setProvince("Saskatchewan");
            $address->setCountry("Canada");
            $property->setAddress($address);
            //$property->setContacts(new ArrayCollection(array($contact)));
            $propertiesArray[] = $property;

            $propertyRepository = $this->em->getRepository(Property::class);
            //save contact to database
            $propertyRepository->save($property);
        }

        //add the property to the contact
        $contact->setProperties(new ArrayCollection($propertiesArray));

        $contactRepository = $this->em->getRepository(Contact::class);
        //save contact to database
        $contactId = $contactRepository->save($contact);

        // Create a client,
        $client = static::createClient(array(), array('PHP_AUTH_USER' => 'admin', 'PHP_AUTH_PW'   => 'password'));

        // A crawler to check if the page contains a search field
        $crawler = $client->request('GET', "/contact/$contactId");

        // Make sure there are 15 properties listed
        $this->assertEquals(15, $crawler->filter('td:contains("Sutherland")')->count());
    }

    /**
     * Story 4k
     * Tests that a property can be associated to a contact
     */
    public function testAssociatePropertySuccess()
    {
        // Create a client,
        $client = static::createClient(array(), array('PHP_AUTH_USER' => 'admin', 'PHP_AUTH_PW'   => 'password'));
        $client->followRedirects(true);

        // Go to the contact view page for Bill Smith
        $crawler = $client->request('GET', "/contact/23");

        //get the form for the add button
        $form = $crawler->selectButton('Add')->form();

        //select the first property (456 West Street)
        $form['appbundle_propertyToContact[property]'] = 1;

        //submit the form
        $crawler = $client->submit($form);

        $this->assertContains("456 West Street",$crawler->filter("#associatedProperties")->html());
    }

    /**
     * Story 4k
     * Tests that an error message is displayed if the property is already associated (the first property "Balla Highrize" is already associated)
     */
    public function testAssociatePropertyDuplicateFailure()
    {
        // Create a client,
        $client = static::createClient(array(), array('PHP_AUTH_USER' => 'admin', 'PHP_AUTH_PW'   => 'password'));
        $client->followRedirects(true);

        // Go to the contact view page for Bill Jones
        $crawler = $client->request('GET', "/contact/24");

        //get the form for the add button
        $form = $crawler->selectButton('Add')->form();

        //select the first property (456 West Street)
        $form['appbundle_propertyToContact[property]'] = 1;

        //submit the form
        $crawler = $client->submit($form);

        $this->assertContains("This contact is already associated to the selected property", $client->getResponse()->getContent());
    }

    /**
     * Story 4k
     * Tests that a property can be successfully removed from a contact
     */
    public function testAssociatePropertyRemoveSuccess()
    {
        // Create a client,
        $client = static::createClient(array(), array('PHP_AUTH_USER' => 'admin', 'PHP_AUTH_PW'   => 'password'));
        $client->followRedirects(true);

        // Go to the contact view page for Bill Jones
        $crawler = $client->request('GET', "/contact/24");

        // "456 West Street" is in the list of properties
        $this->assertContains("456 West Street", $crawler->filter("#associatedProperties")->html());

        //get the form for the add button
        $form = $crawler->selectButton('rmb1')->form();

        //submit the form
        $crawler = $client->submit($form);

        // "Balla Highrize" has been removed
        $this->assertNotContains("456 West Street", $crawler->filter("#associatedProperties")->html());
    }

    /**
     * Story 4k
     * Tests that a user is redirected to the Contact Search page when the remove fails (user goes straight to the remove url)
     */
    public function testAssociatePropertyRemoveURLFailure()
    {
        // Create a client,
        $client = static::createClient(array(), array('PHP_AUTH_USER' => 'admin', 'PHP_AUTH_PW'   => 'password'));
        $client->followRedirects(true);

        // User goes straight to the remove properties url
        $client->request('GET', "/contact/removepropertyfromcontact");

        // Check that we are on the Contact Search page based on the header on the page
        $this->assertContains("Contact Search", $client->getResponse()->getContent());
    }



    /**
     * Story 4k
     * Tests that a contacts properties are displayed in alphabetical order
     */
    public function testAssociatePropertiesAlpahbetical()
    {
        // Create a client,
        $client = static::createClient(array(), array('PHP_AUTH_USER' => 'admin', 'PHP_AUTH_PW'   => 'password'));
        $client->followRedirects(true);

        // Go to the contact view page for Bill Jones
        $crawler = $client->request('GET', "/contact/24");

        ////check that the first row has the Balla Highrize property first (alphabetically first)
        //$this->assertContains('Balla Highrize', $crawler->filter('#associatedProperties tr:nth-child(1)')->html());
        ////check that thug muny apts is in the second row because it should come after Balla Highrize
        //$this->assertContains('Thug Muny Apts.', $crawler->filter('#associatedProperties tr:nth-child(2)')->html());

        $tableRows = $crawler->filter("#associatedProperties tr td:first-child");

        $tableRows->each(function ($node, $i) {
            static $previousRow;
            if($previousRow != null)
            {
                $this->assertGreaterThan($previousRow->text(), $node->text());
            }

            $previousRow = $node;
        });

    }

    /**
     * Story 4k
     * Tests that the add form still exists if a contact has no properties
     */
    public function testNoPropertiesAddForm()
    {
        // Create a client,
        $client = static::createClient(array(), array('PHP_AUTH_USER' => 'admin', 'PHP_AUTH_PW'   => 'password'));
        $client->followRedirects(true);

        // Go to the contact view page for Bill Smith
        $crawler = $client->request('GET', "/contact/23");

        //check that the form exists
        $this->assertEquals(1, $crawler->filter("form[name='appbundle_propertyToContact']")->count());

        //check that the table containing associated properties does not
        $this->assertEquals(0,$crawler->filter("#associatedProeprties")->count());
    }

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
        $stmt = $this->em->getConnection()->prepare("DELETE FROM Property");
        $stmt->execute();
        $stmt = $this->em->getConnection()->prepare("DELETE FROM Contact_Properties");
        $stmt->execute();
        $stmt = $this->em->getConnection()->prepare('DELETE FROM User');
        $stmt->execute();

        $this->em->close();
        $this->em = null;//avoid memory meaks
    }
}

