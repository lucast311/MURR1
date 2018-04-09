<?php
namespace Tests\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use AppBundle\Entity\Property;
use AppBundle\Entity\Address;
use AppBundle\Entity\Container;
use AppBundle\Entity\Communication;
use Doctrine\Common\Collections\ArrayCollection;

use AppBundle\Services\SearchNarrower;
use AppBundle\DataFixtures\ORM\LoadPropertyData;
use AppBundle\Entity\Contact;
use AppBundle\DataFixtures\ORM\LoadUserData;
use Tests\AppBundle\DatabasePrimer;


class PropertyControllerTest extends WebTestCase
{
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

        $propertyLoader = new LoadPropertyData();
        $propertyLoader->load($this->em);

        // Load the admin user into the database so they can log in
        $encoder = static::$kernel->getContainer()->get('security.password_encoder');

        $userLoader = new LoadUserData($encoder);
        $userLoader->load($this->em);
    }

    /**
     *
     * This test will check that you can access the route, populate fields,
     * submit the form, view the success message, and that the fields will
     * be blank after a success
     */
    public function testFormSuccess()
    {
        $client = static::createClient(array(), array('PHP_AUTH_USER' => 'admin', 'PHP_AUTH_PW'   => 'password'));

        $crawler = $client->request('GET', '/property/new');

        $form = $crawler->selectButton('Add')->form();

        //set form values
        $form['appbundle_property[siteId]'] = 1593843;
        $form['appbundle_property[propertyName]'] = 'Charlton Arms';
        $form['appbundle_property[propertyType]'] = 'Townhouse Condo';
        $form['appbundle_property[propertyStatus]'] = 'Active';
        $form['appbundle_property[structureId]'] = 54586;
        $form['appbundle_property[numUnits]'] = 5;
        $form['appbundle_property[neighbourhoodName]'] = 'Sutherland';
        $form['appbundle_property[neighbourhoodId]'] = 'O48';
        $form['appbundle_property[address][streetAddress]'] = '123 Main Street';
        $form['appbundle_property[address][postalCode]'] = 'S7N 0R7';
        $form['appbundle_property[address][city]'] = 'Saskatoon';
        $form['appbundle_property[address][province]'] = 'Saskatchewan';
        $form['appbundle_property[address][country]'] = 'Canada';


        //Remove the property from the database if it already exists so we can insert this one
        //$em = $client->getContainer()->get('doctrine.orm.entity_manager');
        //$stmt = $em->getConnection()->prepare('DELETE FROM Property WHERE id = 1593843');
        //$stmt->execute();
        //$em->close();



        $crawler = $client->submit($form);

        $this->assertContains("Successfully added property",$client->getResponse()->getContent());

        //Refresh the form because a new one was created after submission
        $form = $crawler->selectButton('Add')->form();

        //test that all fields are now empty
        //$this->assertEmpty($form['communication[date][year]']->getValue());
        $this->assertEmpty($form['appbundle_property[siteId]']-> getValue());
        $this->assertEmpty($form['appbundle_property[propertyName]']-> getValue());
        $this->assertEmpty($form['appbundle_property[propertyType]']-> getValue());
        $this->assertEmpty($form['appbundle_property[propertyStatus]']-> getValue());

        $this->assertEmpty($form['appbundle_property[numUnits]']-> getValue());
        $this->assertEmpty($form['appbundle_property[neighbourhoodName]']-> getValue());
        $this->assertEmpty($form['appbundle_property[neighbourhoodId]']-> getValue());
        $this->assertEmpty($form['appbundle_property[address][streetAddress]']-> getValue());
        $this->assertEmpty($form['appbundle_property[address][postalCode]']-> getValue());
        $this->assertEquals($form['appbundle_property[address][city]']-> getValue(),"Saskatoon");
        $this->assertEquals($form['appbundle_property[address][province]']-> getValue(),"Saskatchewan");
        $this->assertEquals($form['appbundle_property[address][country]']-> getValue(),"Canada");
    }

    /**
     * This test will submit the form and check that an error message is displayed
     */
    public function testErrorMessage()
    {
        $client = static::createClient(array(), array('PHP_AUTH_USER' => 'admin', 'PHP_AUTH_PW'   => 'password'));

        $crawler = $client->request('GET', '/property/new');

        $form = $crawler->selectButton('Add')->form();

        //set form values
        $form['appbundle_property[siteId]'] = 1593843;
        $form['appbundle_property[propertyName]'] = 'Charlton Arms';
        $form['appbundle_property[propertyType]'] = 'Townhouse Condo';
        $form['appbundle_property[propertyStatus]'] = 'Active';
        $form['appbundle_property[structureId]'] = 54586;
        $form['appbundle_property[numUnits]'] = 5;
        $form['appbundle_property[neighbourhoodName]'] = '';
        $form['appbundle_property[neighbourhoodId]'] = 'O48';
        $form['appbundle_property[address][streetAddress]'] = '123 Main Street';
        $form['appbundle_property[address][postalCode]'] = 'S7N 0R7';
        $form['appbundle_property[address][city]'] = 'Saskatoon';
        $form['appbundle_property[address][province]'] = 'Saskatchewan';
        $form['appbundle_property[address][country]'] = 'Canada';

        $crawler = $client->submit($form);

        $this->assertContains("Please specify a neighbourhood name",$client->getResponse()->getContent());
    }

    /**
     * This test will submit the form and check that an error message is displayed
     */
    public function testsiteIdDuplicate()
    {
        $client = static::createClient(array(), array('PHP_AUTH_USER' => 'admin', 'PHP_AUTH_PW'   => 'password'));

        $crawler = $client->request('GET', '/property/new');

        for ($i = 0; $i < 2; $i++)
        {

            $form = $crawler->selectButton('Add')->form();

            //set form values
            $form['appbundle_property[siteId]'] = 1593843;
            $form['appbundle_property[propertyName]'] = 'Charlton Arms';
            $form['appbundle_property[propertyType]'] = 'Townhouse Condo';
            $form['appbundle_property[propertyStatus]'] = 'Active';
            $form['appbundle_property[structureId]'] = 54586;
            $form['appbundle_property[numUnits]'] = 5;
            $form['appbundle_property[neighbourhoodName]'] = 'Sutherland';
            $form['appbundle_property[neighbourhoodId]'] = 'O48';
            $form['appbundle_property[address][streetAddress]'] = '123 Main Street';
            $form['appbundle_property[address][postalCode]'] = 'S7N 0R7';
            $form['appbundle_property[address][city]'] = 'Saskatoon';
            $form['appbundle_property[address][province]'] = 'Saskatchewan';
            $form['appbundle_property[address][country]'] = 'Canada';

            $crawler = $client->submit($form);
        }
        $this->assertContains("Site Id already exists",$client->getResponse()->getContent());
    }

    /**
     * This test will load the update page and attempt to edit it
     * Story 4c User edits property
     */
    public function testEditPropertySuccess()
    {
        //Create a new property to ensure that there is one to edit in the database
        $property = new Property();
        $property->setSiteId(1593843);
        $property->setPropertyName("Charlton Arms");
        $property->setPropertyType("Townhouse Condo");
        $property->setPropertyStatus("Active");
        $form['appbundle_property[structureId]'] = 54586;
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

        $client = static::createClient(array(), array('PHP_AUTH_USER' => 'admin', 'PHP_AUTH_PW'   => 'password'));

        //Get the entity manager and the repo so we can make sure a property exists before editing it
        $em = $client->getContainer()->get('doctrine.orm.entity_manager');
        $repo = $em->getRepository(Property::class);
        //insert the property
        $propertyId = $repo->save($property);

        $crawler = $client->request('GET', "/property/$propertyId/edit");

        $form = $crawler->selectButton('Save')->form();

        //set form values
        $form['appbundle_property[propertyName]'] = "Charlton Legs";

        $client->followRedirects(true);

        //Submit the form
        $client->submit($form);

        //$clientResponse = $crawler->filter("html:contains('View property')");
        //Make sure the form has the same values
        $clientResponse = $client->getResponse()->getContent();


        $this->assertContains('Charlton Legs', $clientResponse);


        //$client->request('GET', "/property/$propertyId");
        //$clientResponse = $client->getResponse()->getContent();
        //$this->assertContains('Charlton Legs', $clientResponse);

        //Code to attempt following the redirect, but it will not actually
        //follow the redirect, works in actual implementation though

        //$dbProp = $repo->findOneById($propertyId);



        //$crawler = $client->request('GET', "/property/$propertyId");


        //$clientResponse = $crawler->html();
        //assert that the page contains the updated data

        //assert that the page is the view property page
        $this->assertContains('View Property', $clientResponse);
    }

    /**
     * This test will load the update page and attempt to edit it
     * The page should contain an error
     * Story 4c User edits property
     */
    public function testEditPropertyError()
    {
        //Create a new property to ensure that there is one to edit in the database
        $property = new Property();
        $property->setSiteId(1593844);
        $property->setPropertyName("Charlton Arms");
        $property->setPropertyType("Townhouse Condo");
        $property->setPropertyStatus("Active");
        $property->setStructureId(54586);
        $property->setNumUnits(5);
        $property->setNeighbourhoodName("Sutherland");
        $property->setNeighbourhoodId("O48");
        // Have to create a new valid address too otherwise doctrine will fail
        $address = new Address();
        $address->setStreetAddress("12 15th st east");
        $address->setPostalCode("S0E1A0");
        $address->setCity("Saskatoon");
        $address->setProvince("Saskatchewan");
        $address->setCountry("Canada");
        $property->setAddress($address);


        $client = static::createClient(array(), array('PHP_AUTH_USER' => 'admin', 'PHP_AUTH_PW'   => 'password'));

        //Get the entity manager and the repo so we can make sure a property exists before editing it
        $em = $client->getContainer()->get('doctrine.orm.entity_manager');
        $repo = $em->getRepository(Property::class);
        //insert the property
        $propertyId = $repo->save($property);



        $crawler = $client->request('GET', "/property/$propertyId/edit");

        $form = $crawler->selectButton('Save')->form();

        //set form values
        $form['appbundle_property[siteId]'] = 1593844;
        //Change the property name to test if it is staying on the page
        $form['appbundle_property[propertyName]'] = 'Charlton Armies';
        $form['appbundle_property[propertyType]'] = 'Townhouse Condo';
        $form['appbundle_property[propertyStatus]'] = 'Active';
        $form['appbundle_property[structureId]'] = 54586;
        $form['appbundle_property[numUnits]'] = -5;
        $form['appbundle_property[neighbourhoodName]'] = 'Sutherland';
        $form['appbundle_property[neighbourhoodId]'] = 'O48';
        $form['appbundle_property[address][streetAddress]'] = '123 Main Street';
        $form['appbundle_property[address][postalCode]'] = 'S7N 0R7';
        $form['appbundle_property[address][city]'] = 'Saskatoon';
        $form['appbundle_property[address][province]'] = 'Saskatchewan';
        $form['appbundle_property[address][country]'] = 'Canada';

        $crawler = $client->submit($form);

        //assert that the page contains the error message
        $this->assertContains('Please specify a valid number of units', $client->getResponse()->getContent());

        //assert that the page still contains any changed data
        $this->assertContains('-5', $client->getResponse()->getContent());
        $this->assertContains('Charlton Armies', $client->getResponse()->getContent());
    }

    ///**
    // * This method will test that an error appears if the user does not enter an ID
    // * Story 4c User edits property
    // */
    //public function testEditPropetyNoId(){
    //    //Create a client to go through the web page
    //    $client = static::createClient(array(), array('PHP_AUTH_USER' => 'admin', 'PHP_AUTH_PW'   => 'password'));

    //    //request the property edit page without specifying an ID
    //    $crawler = $client->request('GET', "/property//edit");

    //    //Check if the appropriate error message exists on the page
    //    $this->assertContains("No property specified", $client->getResponse()->getContent());
    //}

    /**
     * This method will test that an error appears if the user enters an invalid ID into the address bar
     * Story 4c User edits property
     */
    public function testEditPropertyBadId()
    {
        //Create a client to go through the web page
        $client = static::createClient(array(), array('PHP_AUTH_USER' => 'admin', 'PHP_AUTH_PW'   => 'password'));

        //request the property edit page without specifying an ID
        $crawler = $client->request('GET', "/property/-5/edit");

        //Check if the appropriate error message exists on the page
        $this->assertContains("The specified property could not be found", $client->getResponse()->getContent());
    }

    /**
     * Story 4b
     * Tests the viewing of a specific property. Ensures that the page can be navigated to
     * and that it contains all the required labels on the page.
     */
    public function testViewActionSuccess()
    {
        //Create a new property to ensure that there is one to view in the database
        $property = new Property();
        $property->setSiteId(1593843);
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

        //Create a client to go through the web page
        $client = static::createClient(array(), array('PHP_AUTH_USER' => 'admin', 'PHP_AUTH_PW'   => 'password'));

        //Get the entity manager and the repo so we can make sure a property exists before editing it
        $em = $client->getContainer()->get('doctrine.orm.entity_manager');
        $repo = $em->getRepository(Property::class);
        //insert the property
        $propertyId = $repo->save($property);


        //Request the property view page for the property that was just inserted
        $crawler = $client->request('GET',"/property/$propertyId");

        // Assert that all the proper labels are on the page
        $this->assertGreaterThan(0, $crawler->filter('html:contains("Site Id")')->count());
        $this->assertGreaterThan(0, $crawler->filter('html:contains("Property Name:")')->count());
        $this->assertGreaterThan(0, $crawler->filter('html:contains("Property Type:")')->count());
        $this->assertGreaterThan(0, $crawler->filter('html:contains("Property Status:")')->count());
        $this->assertGreaterThan(0, $crawler->filter('html:contains("Num Units:")')->count());
        $this->assertGreaterThan(0, $crawler->filter('html:contains("Neighbourhood Name:")')->count());
        $this->assertGreaterThan(0, $crawler->filter('html:contains("Neighbourhood Id:")')->count());
        $this->assertGreaterThan(0, $crawler->filter('html:contains("Street Address:")')->count());
        $this->assertGreaterThan(0, $crawler->filter('html:contains("Postal Code:")')->count());
        $this->assertGreaterThan(0, $crawler->filter('html:contains("City:")')->count());
        $this->assertGreaterThan(0, $crawler->filter('html:contains("Province:")')->count());
        $this->assertGreaterThan(0, $crawler->filter('html:contains("Country:")')->count());

        // Assert that all the data is also there
        $this->assertGreaterThan(0, $crawler->filter('html:contains("1593843")')->count());
        $this->assertGreaterThan(0, $crawler->filter('html:contains("Charlton Arms")')->count());
        $this->assertGreaterThan(0, $crawler->filter('html:contains("Townhouse Condo")')->count());
        $this->assertGreaterThan(0, $crawler->filter('html:contains("Active")')->count());
        $this->assertGreaterThan(0, $crawler->filter('html:contains("5")')->count());
        $this->assertGreaterThan(0, $crawler->filter('html:contains("Sutherland")')->count());
        $this->assertGreaterThan(0, $crawler->filter('html:contains("O48")')->count());
        $this->assertGreaterThan(0, $crawler->filter('html:contains("12 15th st east")')->count());
        $this->assertGreaterThan(0, $crawler->filter('html:contains("S0E 1A0")')->count());
        $this->assertGreaterThan(0, $crawler->filter('html:contains("Saskatoon")')->count());
        $this->assertGreaterThan(0, $crawler->filter('html:contains("Saskatchewan")')->count());
        $this->assertGreaterThan(0, $crawler->filter('html:contains("Canada")')->count());
    }

    /**
     * Story 4b
     * Tests that an error message appears if the user navigates to a bad property id
     */
    public function testViewBadId()
    {
        //Create a client to go through the web page
        $client = static::createClient(array(), array('PHP_AUTH_USER' => 'admin', 'PHP_AUTH_PW'   => 'password'));

        //Request the property view page for a property that does not exist
        $crawler = $client->request('GET',"/property/-5");

        // assert that the correct error message appeared
        $this->assertGreaterThan(0, $crawler->filter('html:contains("The specified property could not be found")')->count());
    }

    /**
     * Story 4b
     * Tests that an error message appears if the user does not enter an id
     */
    public function testViewNoId()
    {
        //Create a client to go through the web page
        $client = static::createClient(array(), array('PHP_AUTH_USER' => 'admin', 'PHP_AUTH_PW'   => 'password'));

        //Request the property view page without specifying an id
        $crawler = $client->request('GET',"/property/");

        // assert that the correct error message appeared
        $this->assertGreaterThan(0, $crawler->filter('html:contains("No property specified")')->count());
    }



    /**
     * Story 4d
     * test that the query successfully returns records in JSON format
     */
    public function testSuccessfullyReceiveSearch()
    {
        // get a repository so we can query for data
        $repository = $this->em->getRepository(Property::class);

        // create a client so we can view the page
        $client = static::createClient(array(), array('PHP_AUTH_USER' => 'admin', 'PHP_AUTH_PW'   => 'password'));

        // go to the page and search for 'Charlton'
        $client->request('GET', '/property/jsonsearch/Charlton');

        // create an array so we can call the search
        $queryStrings = array();
        $queryStrings[] = 'Charlton';

        // query the database
        $repository->propertySearch($queryStrings);

        // assert that what we expect is actually returned
        //$this->assertTrue(false);
        $this->assertContains('[{"id":1,"siteId":3593843,"propertyName":"Charlton Arms","propertyType":"Townhouse Condo","propertyStatus":"Active","structureId":54586,"numUnits":5,', $client->getResponse()->getContent());
    }

    /**
     * Story 4d
     * test that the query to search on is too long
     */
    public function testQueryTooLong()
    {
        // create a client so we can view the page
        $client = static::createClient(array(), array('PHP_AUTH_USER' => 'admin', 'PHP_AUTH_PW'   => 'password'));

        // go to the page and search for 'CharltonArmsCharltonArmsCharltonArmsCharltonArmsCharltonArmsCharltonArmsCharltonArmsCharltonArmsCharltonArms'
        $client->request('GET', '/property/jsonsearch/CharltonArmsCharltonArmsCharltonArmsCharltonArmsCharltonArmsCharltonArmsCharltonArmsCharltonArmsCharltonArms');

        // assert that what we expect is actually returned
        $this->assertContains('[]', $client->getResponse()->getContent());
    }

    /**
     * Story 4h
     * Tests that the list of containers appears when a user views a property that has containers associated with it
     */
    public function testViewContainers()
    {
        //Create a new property to ensure that there is one to view in the database
        $property = new Property();
        $property->setSiteId(55555555);
        $property->setPropertyName("Charlton Arms");
        $property->setPropertyType("Townhouse Condo");
        $property->setPropertyStatus("Active");
        $property->setNumUnits(12);
        $property->setNeighbourhoodName("Sutherland");
        $property->setNeighbourhoodId("O48");

        // Have to create a new valid address too otherwise doctrine will fail
        $address = new Address();
        $address->setStreetAddress("123 Sutherland land");
        $address->setPostalCode("S7N 3K5");
        $address->setCity("Saskatoon");
        $address->setProvince("Saskatchewan");
        $address->setCountry("Canada");
        $property->setAddress($address);


        // WE WILL NEED TO GO INTO PROPERTY AND ADD FUNCTIONALITY TO THE setBins($bins) METHOD
        // IF WE DON'T, THEN WE HAVE NO WAY OF LINKING A PROPERTY TO A LIST OF BINS IN THE CODE


        $container = new Container();
        $container->setContainerSerial("W114-320-001");
        $container->setType("Bin");
        $container->setSize("6 yd");
        $container->setStatus("Active");


        // Add the bin to an array that we will loop through and add to the property
        $bins = array($container);

        // Link the container to the property
        $property->setBins($bins);

        //Create a client to go through the web page
        $client = static::createClient(array(), array('PHP_AUTH_USER' => 'admin', 'PHP_AUTH_PW'   => 'password'));

        //Get the entity manager and the repo so we can make sure a property exists before editing it
        $em = $client->getContainer()->get('doctrine.orm.entity_manager');
        $repo = $em->getRepository(Property::class);

        //insert the property
        $propertyId = $repo->save($property);

        ////Get the entity manager and the repo so we can add our container to the database
        //$em = $client->getContainer()->get('doctrine.orm.entity_manager');
        //$repo = $em->getRepository(Container::class);

        //insert the container
        //$repo->save($container);


        //Request the property view page for the property that was just inserted
        $crawler = $client->request('GET',"/property/$propertyId");

        // Assert that the page contains a table
        $this->assertTrue($crawler->filter('table.containers')->first() != null);

        // Assert that the table contains all the proper headers
        $this->assertGreaterThan(0, $crawler->filter('html:contains("Serial #")')->count());
        $this->assertGreaterThan(0, $crawler->filter('html:contains("Type")')->count());
        $this->assertGreaterThan(0, $crawler->filter('html:contains("Size")')->count());
        $this->assertGreaterThan(0, $crawler->filter('html:contains("Frequency")')->count());
        $this->assertGreaterThan(0, $crawler->filter('html:contains("Route(s)")')->count());
        $this->assertGreaterThan(0, $crawler->filter('html:contains("Bin Status")')->count());

        // Assert that the table contains all the proper data
        $this->assertGreaterThan(0, $crawler->filter('html:contains("W114-320-001")')->count());
        $this->assertGreaterThan(0, $crawler->filter('html:contains("Bin")')->count());
        $this->assertGreaterThan(0, $crawler->filter('html:contains("6 yd")')->count());


        // Note: Some checks will need to be made in order to test if routes are displayed, once routes are implemented
        //$this->assertGreaterThan(0, $crawler->filter('html:contains("")')->count());
        //$this->assertGreaterThan(0, $crawler->filter('html:contains("")')->count());


        $this->assertGreaterThan(0, $crawler->filter('html:contains("Active")')->count());
    }

    /**
     * Story 4h
     * Tests that the list of containers does not appear when a user views a property that has no containers associated with it
     */
    public function testViewNoContainers()
    {
        //Create a new property to ensure that there is one to view in the database
        $property = new Property();
        $property->setSiteId(55555555);
        $property->setPropertyName("Charlton Arms");
        $property->setPropertyType("Townhouse Condo");
        $property->setPropertyStatus("Active");
        $property->setNumUnits(12);
        $property->setNeighbourhoodName("Sutherland");
        $property->setNeighbourhoodId("O48");

        // Have to create a new valid address too otherwise doctrine will fail
        $address = new Address();
        $address->setStreetAddress("123 Sutherland land");
        $address->setPostalCode("S7N 3K5");
        $address->setCity("Saskatoon");
        $address->setProvince("Saskatchewan");
        $address->setCountry("Canada");
        $property->setAddress($address);

        //Create a client to go through the web page
        $client = static::createClient(array(), array('PHP_AUTH_USER' => 'admin', 'PHP_AUTH_PW'   => 'password'));

        //Get the entity manager and the repo so we can make sure a property exists before editing it
        $em = $client->getContainer()->get('doctrine.orm.entity_manager');
        $repo = $em->getRepository(Property::class);

        //insert the property
        $propertyId = $repo->save($property);

        //Request the property view page for the property that was just inserted
        $crawler = $client->request('GET',"/property/$propertyId");

        //Check that no container table headers exist on this page
        $this->assertEquals(0, $crawler->filter('table.containers:contains("Serial #")')->count());
        $this->assertEquals(0, $crawler->filter('table.containers:contains("Type")')->count());
        $this->assertEquals(0, $crawler->filter('table.containers:contains("Size")')->count());
        $this->assertEquals(0, $crawler->filter('table.containers:contains("Frequency")')->count());
        $this->assertEquals(0, $crawler->filter('table.containers:contains("Route(s)")')->count());
        $this->assertEquals(0, $crawler->filter('table.containers:contains("Bin Status")')->count());

        // Assert that the view page contains a message informing the user that there are no containers
        $this->assertGreaterThan(0, $crawler->filter('html:contains("No containers found for this property")')->count());
    }

    /**
     * Story 4h
     * Tests that the list of containers does not appear when a user views a property with an ivalid id
     */
    public function testViewInvalidPropertyId()
    {
        //Create a client to go through the web page
        $client = static::createClient(array(), array('PHP_AUTH_USER' => 'admin', 'PHP_AUTH_PW'   => 'password'));

        //Request the property view page for a property that does not exist
        $crawler = $client->request('GET',"/property/-5");

        // Assert that the correct error message appeared
        $this->assertGreaterThan(0, $crawler->filter('html:contains("The specified property could not be found")')->count());

        //// Assert that the container table did not appear
        //$this->assertTrue($crawler->filter('table.containers')->first() == null);

        //Check that no container table headers exist on this page
        $this->assertEquals(0, $crawler->filter('table.containers:contains("Serial #")')->count());
        $this->assertEquals(0, $crawler->filter('table.containers:contains("Type")')->count());
        $this->assertEquals(0, $crawler->filter('table.containers:contains("Size")')->count());
        $this->assertEquals(0, $crawler->filter('table.containers:contains("Frequency")')->count());
        $this->assertEquals(0, $crawler->filter('table.containers:contains("Route(s)")')->count());
        $this->assertEquals(0, $crawler->filter('table.containers:contains("Bin Status")')->count());
    }

    /**
     * Story 4h
     * Tests that the list of containers does not appear when a user views a property without specifying an id
     */
    public function testViewNoPropertyId()
    {
        //Create a client to go through the web page
        $client = static::createClient(array(), array('PHP_AUTH_USER' => 'admin', 'PHP_AUTH_PW'   => 'password'));

        //Request the property view page for a property that does not exist
        $crawler = $client->request('GET',"/property/");

        // Assert that the correct error message appeared
        $this->assertGreaterThan(0, $crawler->filter('html:contains("No property specified")')->count());

        //// Assert that the container table did not appear
        //$this->assertTrue($crawler->filter('table')->first() == null);

        //Check that no container table headers exist on this page
        $this->assertEquals(0, $crawler->filter('table.containers:contains("Serial #")')->count());
        $this->assertEquals(0, $crawler->filter('table.containers:contains("Type")')->count());
        $this->assertEquals(0, $crawler->filter('table.containers:contains("Size")')->count());
        $this->assertEquals(0, $crawler->filter('table.containers:contains("Frequency")')->count());
        $this->assertEquals(0, $crawler->filter('table.containers:contains("Route(s)")')->count());
        $this->assertEquals(0, $crawler->filter('table.containers:contains("Bin Status")')->count());
    }

    /**
     * Story 4i
     *  This tests that a list of communications can be viewed for a property
     */
    public function testViewAssociatedCommunicationsSuccess()
    {
        //Create a new property to ensure that there is one to view in the database
        $property = new Property();
        $property->setSiteId(55555556);
        $property->setPropertyName("Charlton Arms");
        $property->setPropertyType("Townhouse Condo");
        $property->setPropertyStatus("Active");
        $property->setStructureId(885412);
        $property->setNumUnits(12);
        $property->setNeighbourhoodName("Sutherland");
        $property->setNeighbourhoodId("O48");

        // Have to create a new valid address too otherwise doctrine will fail
        $address = new Address();
        $address->setStreetAddress("123 Sutherland land");
        $address->setPostalCode("S7N 3K5");
        $address->setCity("Saskatoon");
        $address->setProvince("Saskatchewan");
        $address->setCountry("Canada");
        $property->setAddress($address);

        //Create a client to go through the web page
        $client = static::createClient(array(), array('PHP_AUTH_USER' => 'admin', 'PHP_AUTH_PW'   => 'password'));

        //Get the entity manager and the repo so we can make sure a property exists before adding a communication
        $em = $client->getContainer()->get('doctrine.orm.entity_manager');
        $repo = $em->getRepository(Property::class);

        //insert the property
        $propertyId = $repo->save($property);

        // Create a new communication
        $communication = new Communication();
        $communication->setType("Phone");
        $communication->setMedium("Incoming");
        $communication->setContactName("John Smith");
        $communication->setContactEmail("email@email.com");
        $communication->setContactPhone("306-123-4567");
        $communication->setProperty($property);
        $communication->setCategory("Container");
        $communication->setDescription("Bin will be moved to the eastern side of the building");

        $property->setCommunications(new ArrayCollection(array($communication)));

        // Save the communication too
        $repo = $em->getRepository(Communication::class);
        $repo->insert($communication);

        // You have to create the client a second time or the page won't be up to date...
        $client = static::createClient(array(), array('PHP_AUTH_USER' => 'admin', 'PHP_AUTH_PW'   => 'password'));

        //Request the property view page for the property that was just inserted
        $crawler = $client->request('GET',"/property/$propertyId");

        // Get the id of the communication
        $commID = $communication->getId();
        $commDate = $communication->getDate();

        //Check that there is no error message
        $this->assertNotContains("No communication entries found for this property", $client->getResponse()->getContent());

        // Assert that the table contains all the proper headers
        $this->assertGreaterThan(0, $crawler->filter('table.communications:contains("Date")')->count());
        $this->assertGreaterThan(0, $crawler->filter('table.communications:contains("Type")')->count());
        $this->assertGreaterThan(0, $crawler->filter('table.communications:contains("Direction")')->count());
        $this->assertGreaterThan(0, $crawler->filter('table.communications:contains("Name")')->count());
        $this->assertGreaterThan(0, $crawler->filter('table.communications:contains("Phone")')->count());
        $this->assertGreaterThan(0, $crawler->filter('table.communications:contains("Email")')->count());
        $this->assertGreaterThan(0, $crawler->filter('table.communications:contains("Notes")')->count());

        // Assert that the table contains all the proper data
        $this->assertGreaterThan(0, $crawler->filter("table.communications:contains('$commDate')")->count());
        $this->assertGreaterThan(0, $crawler->filter('table.communications:contains("Phone")')->count());
        $this->assertGreaterThan(0, $crawler->filter('table.communications:contains("Incoming")')->count());
        $this->assertGreaterThan(0, $crawler->filter('table.communications:contains("John Smith")')->count());
        $this->assertGreaterThan(0, $crawler->filter('table.communications:contains("306-123-4567")')->count());
        $this->assertGreaterThan(0, $crawler->filter('table.communications:contains("email@email.com")')->count());
        $this->assertGreaterThan(0, $crawler->filter('table.communications:contains("Bin will be moved to the eastern side of the building")')->count());
    }

    /**
     * Story 4i
     *  This tests that an error message will show up if a property has no communications, and makes sure
     *  that there is no table to display
     */
    public function testNoAssociatedCommunications()
    {
        //Create a new property to ensure that there is one to view in the database
        $property = new Property();
        $property->setSiteId(55555555);
        $property->setPropertyName("Charlton Arms");
        $property->setPropertyType("Townhouse Condo");
        $property->setPropertyStatus("Active");
        $property->setStructureId(885412);
        $property->setNumUnits(12);
        $property->setNeighbourhoodName("Sutherland");
        $property->setNeighbourhoodId("O48");

        // Have to create a new valid address too otherwise doctrine will fail
        $address = new Address();
        $address->setStreetAddress("123 Sutherland land");
        $address->setPostalCode("S7N 3K5");
        $address->setCity("Saskatoon");
        $address->setProvince("Saskatchewan");
        $address->setCountry("Canada");
        $property->setAddress($address);


        //Create a client to go through the web page
        $client = static::createClient(array(), array('PHP_AUTH_USER' => 'admin', 'PHP_AUTH_PW'   => 'password'));

        //Get the entity manager and the repo so we can make sure a property exists before editing it
        $em = $client->getContainer()->get('doctrine.orm.entity_manager');
        $repo = $em->getRepository(Property::class);

        //insert the property
        $propertyId = $repo->save($property);

        //Request the property view page for the property that was just inserted
        $crawler = $client->request('GET',"/property/$propertyId");


        // Assert that the table does not have any headers
        //$this->assertEquals(0, $crawler->filter('table.communications:contains("CommID")')->count());
        $this->assertEquals(0, $crawler->filter('table.communications:contains("Date")')->count());
        $this->assertEquals(0, $crawler->filter('table.communications:contains("Type")')->count());
        $this->assertEquals(0, $crawler->filter('table.communications:contains("Direction")')->count());
        $this->assertEquals(0, $crawler->filter('table.communications:contains("Name")')->count());
        $this->assertEquals(0, $crawler->filter('table.communications:contains("Phone")')->count());
        $this->assertEquals(0, $crawler->filter('table.communications:contains("Email")')->count());
        $this->assertEquals(0, $crawler->filter('table.communications:contains("Notes")')->count());

        //Assert that the error message is on the page
        $this->assertGreaterThan(0, $crawler->filter('html:contains("No communications found for this property")')->count());
    }

    /**
     * Story 4i
     * Tests that the list of communications does not appear when a user views a property with an ivalid id
     */
    public function testViewCommunicationsInvalidPropertyId()
    {
        //Create a client to go through the web page
        $client = static::createClient(array(), array('PHP_AUTH_USER' => 'admin', 'PHP_AUTH_PW'   => 'password'));

        //Request the property view page for a property that does not exist
        $crawler = $client->request('GET',"/property/-5");


        //Check that no communication table headers exist on this page
        $this->assertEquals(0, $crawler->filter('table.communications:contains("Date")')->count());
        $this->assertEquals(0, $crawler->filter('table.communications:contains("Type")')->count());
        $this->assertEquals(0, $crawler->filter('table.communications:contains("Direction")')->count());
        $this->assertEquals(0, $crawler->filter('table.communications:contains("Name")')->count());
        $this->assertEquals(0, $crawler->filter('table.communications:contains("Phone")')->count());
        $this->assertEquals(0, $crawler->filter('table.communications:contains("Email")')->count());
        $this->assertEquals(0, $crawler->filter('table.communications:contains("Notes")')->count());
    }

    /**
     * Story 4i
     *
     */
    public function testViewCommunicationsMany()
    {
        //Create a new property to ensure that there is one to view in the database
        $property = new Property();
        $property->setSiteId(55555555);
        $property->setPropertyName("Charlton Arms");
        $property->setPropertyType("Townhouse Condo");
        $property->setPropertyStatus("Active");
        $property->setStructureId(885412);
        $property->setNumUnits(12);
        $property->setNeighbourhoodName("Sutherland");
        $property->setNeighbourhoodId("O48");

        // Have to create a new valid address too otherwise doctrine will fail
        $address = new Address();
        $address->setStreetAddress("123 Sutherland land");
        $address->setPostalCode("S7N 3K5");
        $address->setCity("Saskatoon");
        $address->setProvince("Saskatchewan");
        $address->setCountry("Canada");
        $property->setAddress($address);

        //Create a client to go through the web page
        $client = static::createClient(array(), array('PHP_AUTH_USER' => 'admin', 'PHP_AUTH_PW'   => 'password'));

        //Get the entity manager and the repo
        $em = $client->getContainer()->get('doctrine.orm.entity_manager');

        $repo = $em->getRepository(Communication::class);

        // Create 15 new communications on this property
        for ($i = 0; $i < 15; $i++)
        {
        	$communication = new Communication();
            $communication->setType("Phone");
            $communication->setMedium("Incoming");
            $communication->setContactName("John Smith $i");
            $communication->setContactEmail("email@email.com");
            $communication->setContactPhone("306-123-4567");
            $communication->setProperty($property);
            $communication->setCategory("Container");
            $communication->setDescription("Bin will be moved to the eastern side of the building");
            // Save it
            $repo->insert($communication);
        }

        $repo = $em->getRepository(Property::class);

        //insert the property
        $propertyId = $repo->save($property);

        // You have to create the client a second time or the page won't be up to date...
        $client = static::createClient(array(), array('PHP_AUTH_USER' => 'admin', 'PHP_AUTH_PW'   => 'password'));

        //Request the property view page for the property that was just inserted
        $crawler = $client->request('GET',"/property/$propertyId");


        // Assert that the table contains 10 rows of data
        $this->assertEquals(10, $crawler->filter("table.communications tbody tr")->count());
    }

    /**
     * Story 4j
     *  This tests that a list of contacts can be viewed for a property
     */
    public function testViewAssociatedContactsSuccess()
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

        //add the property to the contact
        $contact->setProperties(new ArrayCollection(array($property)));

        $repository = $this->em->getRepository(Property::class);
        //save property to database
        $id = $repository->save($property);

        // You have to create the client
        $client = static::createClient(array(), array('PHP_AUTH_USER' => 'admin', 'PHP_AUTH_PW'   => 'password'));

        //Request the property view page for the property that was just inserted
        $crawler = $client->request('GET',"/property/$id");

        //Check that there is no error message
        $this->assertNotContains("No associated contacts", $client->getResponse()->getContent());

        // Assert that the table contains all the proper headers
        $this->assertGreaterThan(0, $crawler->filter('table.contacts:contains("Role")')->count());
        $this->assertGreaterThan(0, $crawler->filter('table.contacts:contains("Name")')->count());
        $this->assertGreaterThan(0, $crawler->filter('table.contacts:contains("Phone")')->count());
        $this->assertGreaterThan(0, $crawler->filter('table.contacts:contains("Email")')->count());
        $this->assertGreaterThan(0, $crawler->filter('table.contacts:contains("Organization")')->count());

        // Assert that the table contains all the proper data
        $this->assertGreaterThan(0, $crawler->filter('table.contacts:contains("Property Manager")')->count());
        $this->assertGreaterThan(0, $crawler->filter('table.contacts:contains("Ashton South")')->count());
        $this->assertGreaterThan(0, $crawler->filter('table.contacts:contains("306-345-8932")')->count());
        $this->assertGreaterThan(0, $crawler->filter('table.contacts:contains("south@gmail.com")')->count());
        $this->assertGreaterThan(0, $crawler->filter('table.contacts:contains("COSMO!")')->count());
    }

    /**
     * Story 4j
     *  This tests that an error message will show up if a property has no contacts, and makes sure
     *  that there is no table to display
     */
    public function testNoAssociatedContacts()
    {
        //Create a new property to ensure that there is one to view in the database
        $property = new Property();
        $property->setSiteId(55555555);
        $property->setPropertyName("Charlton Arms");
        $property->setPropertyType("Townhouse Condo");
        $property->setPropertyStatus("Active");
        $property->setStructureId(885412);
        $property->setNumUnits(12);
        $property->setNeighbourhoodName("Sutherland");
        $property->setNeighbourhoodId("O48");

        // Have to create a new valid address too otherwise doctrine will fail
        $address = new Address();
        $address->setStreetAddress("123 Sutherland land");
        $address->setPostalCode("S7N 3K5");
        $address->setCity("Saskatoon");
        $address->setProvince("Saskatchewan");
        $address->setCountry("Canada");
        $property->setAddress($address);


        //Create a client to go through the web page
        $client = static::createClient(array(), array('PHP_AUTH_USER' => 'admin', 'PHP_AUTH_PW'   => 'password'));

        //Get the entity manager and the repo so we can make sure a property exists before editing it
        $em = $client->getContainer()->get('doctrine.orm.entity_manager');
        $repo = $em->getRepository(Property::class);

        //insert the property
        $propertyId = $repo->save($property);

        //Request the property view page for the property that was just inserted
        $crawler = $client->request('GET',"/property/$propertyId");


        // Assert that the table does not have any headers
        $this->assertEquals(0, $crawler->filter('table.contacts:contains("Role")')->count());
        $this->assertEquals(0, $crawler->filter('table.contacts:contains("Name")')->count());
        $this->assertEquals(0, $crawler->filter('table.contacts:contains("Phone")')->count());
        $this->assertEquals(0, $crawler->filter('table.contacts:contains("Email")')->count());
        $this->assertEquals(0, $crawler->filter('table.contacts:contains("Organization")')->count());

        //Assert that the error message is on the page
        $this->assertGreaterThan(0, $crawler->filter('html:contains("No contacts found for this property")')->count());
    }

    /**
     * Story 4j
     * Tests that the list of contacts does not appear when a user views a property with an ivalid id
     */
    public function testViewContactsInvalidPropertyId()
    {
        //Create a client to go through the web page
        $client = static::createClient(array(), array('PHP_AUTH_USER' => 'admin', 'PHP_AUTH_PW'   => 'password'));

        //Request the property view page for a property that does not exist
        $crawler = $client->request('GET',"/property/-5");


        //Check that no contact table headers exist on this page
        $this->assertEquals(0, $crawler->filter('table.contacts:contains("Role")')->count());
        $this->assertEquals(0, $crawler->filter('table.contacts:contains("Name")')->count());
        $this->assertEquals(0, $crawler->filter('table.contacts:contains("Phone")')->count());
        $this->assertEquals(0, $crawler->filter('table.contacts:contains("Email")')->count());
        $this->assertEquals(0, $crawler->filter('table.contacts:contains("Organization")')->count());
    }

    /**
     * Story 4j
     * Test to make sure that multiple contacts can appear
     */
    public function testViewContactsMany()
    {
        //Create a new property to ensure that there is one to view in the database
        $property = new Property();
        $property->setSiteId(55555555);
        $property->setPropertyName("Charlton Arms");
        $property->setPropertyType("Townhouse Condo");
        $property->setPropertyStatus("Active");
        $property->setStructureId(885412);
        $property->setNumUnits(12);
        $property->setNeighbourhoodName("Sutherland");
        $property->setNeighbourhoodId("O48");

        // Have to create a new valid address too otherwise doctrine will fail
        $address = new Address();
        $address->setStreetAddress("123 Sutherland land");
        $address->setPostalCode("S7N 3K5");
        $address->setCity("Saskatoon");
        $address->setProvince("Saskatchewan");
        $address->setCountry("Canada");
        $property->setAddress($address);

        //Create a client to go through the web page
        //$client = static::createClient(array(), array('PHP_AUTH_USER' => 'admin', 'PHP_AUTH_PW'   => 'password'));

        //Get the entity manager and the repo
        //$em = $client->getContainer()->get('doctrine.orm.entity_manager');

        $contactsArray = array();
        // Create 15 new contacts on this property
        for ($i = 0; $i < 15; $i++)
        {
            //create a contact to insert
            $contact = new Contact();
            $contact->setFirstName("Ashton" . uniqid());
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
            $contact->setProperties(new ArrayCollection(array($property)));
            // add the contact to the array
            $contactsArray[] = $contact;

            //$addressRepository = $this->em->getRepository(Address::class);
            //save address to database
            //$addressRepository->save($address);

            $contactRepository = $this->em->getRepository(Contact::class);
            //save contact to database
            $contactRepository->save($contact);
        }

        // associate the contacts with the property.
        $property->setContacts(new ArrayCollection($contactsArray));

        $repo = $this->em->getRepository(Property::class);

        //insert the property
        $propertyId = $repo->save($property);

        // You have to create the client a second time or the page won't be up to date...
        $client = static::createClient(array(), array('PHP_AUTH_USER' => 'admin', 'PHP_AUTH_PW'   => 'password'));

        //Request the property view page for the property that was just inserted
        $crawler = $client->request('GET',"/property/$propertyId");


        // Assert that the table contains 15 rows of data
        $this->assertEquals(15, $crawler->filter("table.contacts tbody tr")->count());
    }


    /**
     * Story 4f
     * test that the search page is accessable and that there is the proper elements on screen.
     */
    public function testSearchPageAccessible()
    {
        // Create a client, and go to the search page for a property
        $client = static::createClient(array(), array('PHP_AUTH_USER' => 'admin', 'PHP_AUTH_PW'   => 'password'));

        // A crawler to check if the page contains a search field
        $crawler = $client->request('GET', '/property/search');

        // Assert that the page contains both a Header, and a search field
        $this->assertContains("Property Search", $client->getResponse()->getContent());
        $this->assertTrue($crawler->filter('input[type=search]')->first() != null);
    }




    /**
     * (@inheritDoc)
     */
    protected function tearDown()
    {
        parent::tearDown();

        // Delete all the things that were just inserted. Or literally everything.
        $client = static::createClient(array(), array('PHP_AUTH_USER' => 'admin', 'PHP_AUTH_PW'   => 'password'));
        $em = $client->getContainer()->get('doctrine.orm.entity_manager');
        $stmt = $em->getConnection()->prepare('DELETE FROM Property');
        $stmt->execute();
        $stmt = $em->getConnection()->prepare('DELETE FROM Container');
        $stmt->execute();
        $stmt = $em->getConnection()->prepare('DELETE FROM Address');
        $stmt->execute();
        $stmt = $em->getConnection()->prepare('DELETE FROM Communication');
        $stmt->execute();
        $stmt = $em->getConnection()->prepare('DELETE FROM Contact');
        $stmt->execute();
        $stmt = $em->getConnection()->prepare('DELETE FROM Contact_Properties');
        $stmt->execute();
        $stmt = $em->getConnection()->prepare('DELETE FROM property_contact');
        $stmt->execute();
        $stmt = $em->getConnection()->prepare('DELETE FROM User');
        $stmt->execute();
        $em->close();

    }
}