<?php
namespace Tests\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use AppBundle\Entity\Property;
use AppBundle\Entity\Address;
use AppBundle\Entity\Container;

use AppBundle\Services\SearchNarrower;
use AppBundle\DataFixtures\ORM\LoadPropertyData;


class PropertyControllerTest extends WebTestCase
{
    private $em;

    protected function setUp()
    {
        self::bootKernel();
        $this->em = static::$kernel->getContainer()
            ->get('doctrine')
            ->getManager();

        $propertyLoader = new LoadPropertyData();
        $propertyLoader->load($this->em);
    }

    /**
     *
     * This test will check that you can access the route, populate fields,
     * submit the form, view the success message, and that the fields will
     * be blank after a success
     */
    public function testFormSuccess()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/property/add');

        $form = $crawler->selectButton('Submit')->form();

        //set form values
        $form['property[siteId]'] = 1593843;
        $form['property[propertyName]'] = 'Charlton Arms';
        $form['property[propertyType]'] = 'Townhouse Condo';
        $form['property[propertyStatus]'] = 'Active';
        $form['property[numUnits]'] = 5;
        $form['property[neighbourhoodName]'] = 'Sutherland';
        $form['property[neighbourhoodId]'] = 'O48';
        $form['property[address][streetAddress]'] = '123 Main Street';
        $form['property[address][postalCode]'] = 'S7N 0R7';
        $form['property[address][city]'] = 'Saskatoon';
        $form['property[address][province]'] = 'Saskatchewan';
        $form['property[address][country]'] = 'Canada';

        //Remove the property from the database if it already exists so we can insert this one
        //$em = $client->getContainer()->get('doctrine.orm.entity_manager');
        //$stmt = $em->getConnection()->prepare('DELETE FROM Property WHERE id = 1593843');
        //$stmt->execute();
        //$em->close();



        $crawler = $client->submit($form);

        $this->assertContains("Successfully added property",$client->getResponse()->getContent());

        //Refresh the form because a new one was created after submission
        $form = $crawler->selectButton('Submit')->form();

        //test that all fields are now empty
        //$this->assertEmpty($form['communication[date][year]']->getValue());
        $this->assertEmpty($form['property[siteId]']-> getValue());
        $this->assertEmpty($form['property[propertyName]']-> getValue());
        $this->assertEmpty($form['property[propertyType]']-> getValue());
        $this->assertEmpty($form['property[propertyStatus]']-> getValue());
        $this->assertEmpty($form['property[numUnits]']-> getValue());
        $this->assertEmpty($form['property[neighbourhoodName]']-> getValue());
        $this->assertEmpty($form['property[neighbourhoodId]']-> getValue());
        $this->assertEmpty($form['property[address][streetAddress]']-> getValue());
        $this->assertEmpty($form['property[address][postalCode]']-> getValue());
        $this->assertEquals($form['property[address][city]']-> getValue(),"Saskatoon");
        $this->assertEquals($form['property[address][province]']-> getValue(),"Saskatchewan");
        $this->assertEquals($form['property[address][country]']-> getValue(),"Canada");
    }

    /**
     * This test will submit the form and check that an error message is displayed
     */
    public function testErrorMessage()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/property/add');

        $form = $crawler->selectButton('Submit')->form();

        //set form values
        $form['property[siteId]'] = 1593843;
        $form['property[propertyName]'] = 'Charlton Arms';
        $form['property[propertyType]'] = 'Townhouse Condo';
        $form['property[propertyStatus]'] = 'Active';
        $form['property[numUnits]'] = 5;
        $form['property[neighbourhoodName]'] = '';
        $form['property[neighbourhoodId]'] = 'O48';
        $form['property[address][streetAddress]'] = '123 Main Street';
        $form['property[address][postalCode]'] = 'S7N 0R7';
        $form['property[address][city]'] = 'Saskatoon';
        $form['property[address][province]'] = 'Saskatchewan';
        $form['property[address][country]'] = 'Canada';

        $crawler = $client->submit($form);

        $this->assertContains("Please specify a neighbourhood name",$client->getResponse()->getContent());
    }

    /**
     * This test will submit the form and check that an error message is displayed
     */
    public function testsiteIdDuplicate()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/property/add');

        for ($i = 0; $i < 2; $i++)
        {

            $form = $crawler->selectButton('Submit')->form();

            //set form values
            $form['property[siteId]'] = 1593843;
            $form['property[propertyName]'] = 'Charlton Arms';
            $form['property[propertyType]'] = 'Townhouse Condo';
            $form['property[propertyStatus]'] = 'Active';
            $form['property[numUnits]'] = 5;
            $form['property[neighbourhoodName]'] = 'Sutherland';
            $form['property[neighbourhoodId]'] = 'O48';
            $form['property[address][streetAddress]'] = '123 Main Street';
            $form['property[address][postalCode]'] = 'S7N 0R7';
            $form['property[address][city]'] = 'Saskatoon';
            $form['property[address][province]'] = 'Saskatchewan';
            $form['property[address][country]'] = 'Canada';

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

        $client = static::createClient();

        //Get the entity manager and the repo so we can make sure a property exists before editing it
        $em = $client->getContainer()->get('doctrine.orm.entity_manager');
        $repo = $em->getRepository(Property::class);
        //insert the property
        $propertyId = $repo->save($property);

        $crawler = $client->request('GET', "/property/edit/$propertyId");

        $form = $crawler->selectButton('Submit')->form();

        //set form values
        $form['appbundle_property[propertyName]'] = "Charlton Legs";

        $client->followRedirects(true);

        //Submit the form
        $client->submit($form);

        //$clientResponse = $crawler->filter("html:contains('View property')");
        //Make sure the form has the same values
        $clientResponse = $client->getResponse()->getContent();


        $this->assertContains('Charlton Legs', $clientResponse);


        //$client->request('GET', "/property/view/$propertyId");
        //$clientResponse = $client->getResponse()->getContent();
        //$this->assertContains('Charlton Legs', $clientResponse);

        //Code to attempt following the redirect, but it will not actually
        //follow the redirect, works in actual implementation though

        //$dbProp = $repo->findOneById($propertyId);



        //$crawler = $client->request('GET', "/property/view/$propertyId");


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


        $client = static::createClient();

        //Get the entity manager and the repo so we can make sure a property exists before editing it
        $em = $client->getContainer()->get('doctrine.orm.entity_manager');
        $repo = $em->getRepository(Property::class);
        //insert the property
        $propertyId = $repo->save($property);



        $crawler = $client->request('GET', "/property/edit/$propertyId");

        $form = $crawler->selectButton('Submit')->form();

        //set form values
        $form['appbundle_property[siteId]'] = 1593844;
        //Change the property name to test if it is staying on the page
        $form['property[propertyName]'] = 'Charlton Armies';
        $form['property[propertyType]'] = 'Townhouse Condo';
        $form['property[propertyStatus]'] = 'Active';
        $form['property[numUnits]'] = -5;
        $form['property[neighbourhoodName]'] = 'Sutherland';
        $form['property[neighbourhoodId]'] = 'O48';
        $form['property[address][streetAddress]'] = '123 Main Street';
        $form['property[address][postalCode]'] = 'S7N 0R7';
        $form['property[address][city]'] = 'Saskatoon';
        $form['property[address][province]'] = 'Saskatchewan';
        $form['property[address][country]'] = 'Canada';

        $crawler = $client->submit($form);

        //assert that the page contains the error message
        $this->assertContains('Please specify a valid number of units', $client->getResponse()->getContent());

        //assert that the page still contains any changed data
        $this->assertContains('-5', $client->getResponse()->getContent());
        $this->assertContains('Charlton Armies', $client->getResponse()->getContent());
    }

    /**
     * This method will test that an error appears if the user does not enter an ID
     * Story 4c User edits property
     */
    public function testEditPropetyNoId(){
        //Create a client to go through the web page
        $client = static::createClient();

        //request the property edit page without specifying an ID
        $crawler = $client->request('GET', "/property/edit/");

        //Check if the appropriate error message exists on the page
        $this->assertContains("No property specified", $client->getResponse()->getContent());
    }

    /**
     * This method will test that an error appears if the user enters an invalid ID into the address bar
     * Story 4c User edits property
     */
    public function testEditPropertyBadId()
    {
        //Create a client to go through the web page
        $client = static::createClient();

        //request the property edit page without specifying an ID
        $crawler = $client->request('GET', "/property/edit/-5");

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
        $client = static::createClient();

        //Get the entity manager and the repo so we can make sure a property exists before editing it
        $em = $client->getContainer()->get('doctrine.orm.entity_manager');
        $repo = $em->getRepository(Property::class);
        //insert the property
        $propertyId = $repo->save($property);


        //Request the property view page for the property that was just inserted
        $crawler = $client->request('GET',"/property/view/$propertyId");

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
        $client = static::createClient();

        //Request the property view page for a property that does not exist
        $crawler = $client->request('GET',"/property/view/-5");

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
        $client = static::createClient();

        //Request the property view page without specifying an id
        $crawler = $client->request('GET',"/property/view/");

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
        $client = static::createClient();

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
        $client = static::createClient();

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
        $client = static::createClient();

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
        $crawler = $client->request('GET',"/property/view/$propertyId");

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
        $client = static::createClient();

        //Get the entity manager and the repo so we can make sure a property exists before editing it
        $em = $client->getContainer()->get('doctrine.orm.entity_manager');
        $repo = $em->getRepository(Property::class);

        //insert the property
        $propertyId = $repo->save($property);

        //Request the property view page for the property that was just inserted
        $crawler = $client->request('GET',"/property/view/$propertyId");

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
        $client = static::createClient();

        //Request the property view page for a property that does not exist
        $crawler = $client->request('GET',"/property/view/-5");

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
        $client = static::createClient();

        //Request the property view page for a property that does not exist
        $crawler = $client->request('GET',"/property/view");

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

    protected function tearDown()
    {
        parent::tearDown();

        // Delete all the things that were just inserted. Or literally everything.
        $client = static::createClient();
        $em = $client->getContainer()->get('doctrine.orm.entity_manager');
        $stmt = $em->getConnection()->prepare('DELETE FROM Property');
        $stmt->execute();
        $stmt = $em->getConnection()->prepare('DELETE FROM Container');
        $stmt->execute();
        $stmt = $em->getConnection()->prepare('DELETE FROM Address');
        $stmt->execute();
        $em->close();

    }
}