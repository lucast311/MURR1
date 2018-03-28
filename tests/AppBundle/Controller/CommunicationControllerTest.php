<?php

namespace Tests\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use AppBundle\Entity\Communication;
use AppBundle\Entity\Property;
use AppBundle\Entity\Address;
use AppBundle\DataFixtures\ORM\LoadUserData;
use AppBundle\DataFixtures\ORM\LoadCommunicationData;
use DateTime;
use Tests\AppBundle\DatabasePrimer;
//use Doctrine\Common\Persistence\ObjectRepository;

class CommunicationControllerTest extends WebTestCase
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

        $this->em = static::$kernel->getContainer()
            ->get('doctrine')
            ->getManager();

        // Load the admin user into the database so they can log in
        $encoder = static::$kernel->getContainer()->get('security.password_encoder');

        $userLoader = new LoadUserData($encoder);
        $userLoader->load($this->em);

        $communicationLoader = new LoadCommunicationData();
        $communicationLoader->load($this->em);
    }

    public function testFormSuccess()
    {
        $client = static::createClient(array(), array('PHP_AUTH_USER' => 'admin', 'PHP_AUTH_PW'   => 'password'));

        $crawler = $client->request('GET', '/communication/new');

        $form = $crawler->selectButton('Add')->form();

        //set form values
        //$form['communication[date][year]'] = "2017";
        //$form['communication[date][month]'] = "10";
        //$form['communication[date][day]'] = "5";
        $form['appbundle_communication[type]']="Phone";
        $form['appbundle_communication[medium]']="Incoming";
        //$form['communication[contact]']=1; //contact id
        $form['appbundle_communication[contactName]'] = "John Smith";
        $form['appbundle_communication[contactEmail]'] = "email@email.com";
        $form['appbundle_communication[contactPhone]'] = "123-123-4567";
        //$form['communication[property]']=1; //property id
        $form['appbundle_communication[category]']="Container";
        $form["appbundle_communication[description]"]="Container has graffiti and needs to be cleaned. Action request made";

        $crawler = $client->submit($form);

        $this->assertContains("Communication added successfully",$client->getResponse()->getContent());

        //Refresh the form because a new one was created after submission
        $form = $crawler->selectButton('Add')->form();

        //test that all fields are now empty
            //date will not be empty by default
        //$this->assertEmpty($form['communication[date][year]']->getValue());
        //$this->assertEmpty($form['communication[date][month]']->getValue());
        //$this->assertEmpty($form['communication[date][day]']->getValue());
        $this->assertEmpty($form['appbundle_communication[type]']->getValue());
        $this->assertEmpty($form['appbundle_communication[medium]']->getValue());
        $this->assertEmpty( $form['appbundle_communication[contactName]']->getValue());
        $this->assertEmpty( $form['appbundle_communication[contactEmail]']->getValue());
        $this->assertEmpty( $form['appbundle_communication[contactPhone]']->getValue());
        $this->assertEmpty($form['appbundle_communication[property]']->getValue());
        $this->assertEmpty($form['appbundle_communication[category]']->getValue());
        $this->assertEmpty($form['appbundle_communication[description]']->getValue());
    }

    //public function testFutureDate()
    //{

    //    $client = static::createClient(array(), array('PHP_AUTH_USER' => 'admin', 'PHP_AUTH_PW'   => 'password'));

    //    $crawler = $client->request('GET', '/communication/new');

    //    $form = $crawler->selectButton('Add')->form();

    //    //set form values
    //    $form['communication[date][year]'] = "2019";
    //    $form['communication[date][month]'] = "10";
    //    $form['communication[date][day]'] = "5";

    //    $crawler = $client->submit($form);

    //    $this->assertContains("Please select a current or past date",$client->getResponse()->getContent());
    //}

    //public function testEmptyDate()
    //{
    //    $client = static::createClient(array(), array('PHP_AUTH_USER' => 'admin', 'PHP_AUTH_PW'   => 'password'));

    //    $crawler = $client->request('GET', '/communication/new');

    //    $form = $crawler->selectButton('Add')->form();

    //    //set form values
    //    //do not set date by removing the form fields
    //    $form->remove('communication[date][year]');
    //    $form->remove('communication[date][month]');
    //    $form->remove('communication[date][day]');


    //    $crawler = $client->submit($form);

    //    $this->assertContains("Please select a date",$client->getResponse()->getContent());
    //}

    //public function testNonExistantDate()
    //{
    //    $client = static::createClient(array(), array('PHP_AUTH_USER' => 'admin', 'PHP_AUTH_PW'   => 'password'));

    //    $crawler = $client->request('GET', '/communication/new');

    //    $form = $crawler->selectButton('Add')->form();

    //    //set form values
    //    $form['communication[date][year]'] = "2017";
    //    $form['communication[date][month]'] = "2";
    //    $form['communication[date][day]'] = "30";

    //    $crawler = $client->submit($form);

    //    $this->assertContains("Please select a valid date",$client->getResponse()->getContent());
    //}

    public function testNoType()
    {
        $client = static::createClient(array(), array('PHP_AUTH_USER' => 'admin', 'PHP_AUTH_PW'   => 'password'));

        $crawler = $client->request('GET', '/communication/new');

        $form = $crawler->selectButton('Add')->form();

        //set form values
        $form['appbundle_communication[type]']=0;

        $crawler = $client->submit($form);

        $this->assertContains("Please select a type of communication",$client->getResponse()->getContent());
    }

    public function testNoMedium()
    {
        $client = static::createClient(array(), array('PHP_AUTH_USER' => 'admin', 'PHP_AUTH_PW'   => 'password'));

        $crawler = $client->request('GET', '/communication/new');

        $form = $crawler->selectButton('Add')->form();

        //set form values
        //do not set medium
        //$form['communication[medium]']=0;

        $crawler = $client->submit($form);

        $this->assertContains("Please select a direction",$client->getResponse()->getContent());
    }

    //public function testBlankContact()
    //{
    //    $client = static::createClient(array(), array('PHP_AUTH_USER' => 'admin', 'PHP_AUTH_PW'   => 'password'));

    //    $crawler = $client->request('GET', '/communication/new');

    //    $form = $crawler->selectButton('Add')->form();

    //    //set form values
    //    $form['communication[contact]']=0; //blank contact ID

    //    $crawler = $client->submit($form);

    //    $this->assertContains("Please enter a contact",$client->getResponse()->getContent());
    //}

    //public function testResidentContact()
    //{
    //    $client = static::createClient(array(), array('PHP_AUTH_USER' => 'admin', 'PHP_AUTH_PW'   => 'password'));

    //    $crawler = $client->request('GET', '/communication/new');

    //    $form = $crawler->selectButton('Add')->form();

    //    //set form values
    //    $form['communication[date][year]'] = "2017";
    //    $form['communication[date][month]'] = "10";
    //    $form['communication[date][day]'] = "5";
    //    $form['communication[type]']="phone";
    //    $form['communication[medium]']="incoming";
    //    $form['communication[contact]']=-1; //identifier for a resident, will not be stored
    //    $form['communication[property]']=1;
    //    $form['communication[category]']="container";
    //    $form["communication[description]"]="Container has graffiti and needs to be cleaned. Action request made";

    //    $crawler = $client->submit($form);

    //    $this->assertContains("Communication added successfully",$client->getResponse()->getContent());
    //}

    //public function testBlankProperty()
    //{
    //    $client = static::createClient(array(), array('PHP_AUTH_USER' => 'admin', 'PHP_AUTH_PW'   => 'password'));

    //    $crawler = $client->request('GET', '/communication/new');

    //    $form = $crawler->selectButton('Add')->form();

    //    //set form values
    //    $form['communication[property]']=0; //blank property ID

    //    $crawler = $client->submit($form);

    //    $this->assertContains("Please select a property",$client->getResponse()->getContent());
    //}

    //public function testMultiOrNAProperty()
    //{
    //    $client = static::createClient(array(), array('PHP_AUTH_USER' => 'admin', 'PHP_AUTH_PW'   => 'password'));

    //    $crawler = $client->request('GET', '/communication/new');

    //    $form = $crawler->selectButton('Add')->form();

    //    //set form values
    //    //set form values
    //    $form['communication[date][year]'] = "2017";
    //    $form['communication[date][month]'] = "10";
    //    $form['communication[date][day]'] = "5";
    //    $form['communication[type]']="Phone";
    //    $form['communication[medium]']="Incoming";
    //    $form['communication[contact]']=1; //contact id
    //    $form['communication[property]']=-1; //multi-property or N/A property identifier
    //    $form['communication[category]']="container";
    //    $form["communication[description]"]="Container has graffiti and needs to be cleaned. Action request made";

    //    $crawler = $client->submit($form);

    //    $this->assertContains("Communication added successfully",$client->getResponse()->getContent());
    //}

    public function testBlankCategory()
    {
        $client = static::createClient(array(), array('PHP_AUTH_USER' => 'admin', 'PHP_AUTH_PW'   => 'password'));

        $crawler = $client->request('GET', '/communication/new');

        $form = $crawler->selectButton('Add')->form();

        //set form values
        $form['appbundle_communication[category]']=0; //blank category value


        $crawler = $client->submit($form);

        $this->assertContains("Please select a category",$client->getResponse()->getContent());
    }

    public function testBlankDescription()
    {
        $client = static::createClient(array(), array('PHP_AUTH_USER' => 'admin', 'PHP_AUTH_PW'   => 'password'));

        $crawler = $client->request('GET', '/communication/new');

        $form = $crawler->selectButton('Add')->form();

        //set form values
        $form['appbundle_communication[description]']=""; //blank description


        $crawler = $client->submit($form);

        $this->assertContains("Please provide a brief description of the communication",$client->getResponse()->getContent());
    }

    //public function testShortDescription()
    //{
    //    $client = static::createClient(array(), array('PHP_AUTH_USER' => 'admin', 'PHP_AUTH_PW'   => 'password'));

    //    $crawler = $client->request('GET', '/communication/new');

    //    $form = $crawler->selectButton('Add')->form();

    //    //set form values
    //    $form['communication[description]']="Talked"; //description too short


    //    $crawler = $client->submit($form);

    //    $this->assertContains("Please provide a description of 50 characters or more",$client->getResponse()->getContent());
    //}

    public function testLongDescription()
    {
        $client = static::createClient(array(), array('PHP_AUTH_USER' => 'admin', 'PHP_AUTH_PW'   => 'password'));

        $crawler = $client->request('GET', '/communication/new');

        $form = $crawler->selectButton('Add')->form();


        //set form values
        $form['appbundle_communication[description]']=str_repeat('a',501);//generate a string that is too long


        $crawler = $client->submit($form);

        $this->assertContains("Description must be 500 characters or less",$client->getResponse()->getContent());
    }



    /**
     * Story 11b
     * Tests that you can view a communication entry with the proper information
     */
    public function testViewActionSuccess(){

        //create a property for the communication
        $property = new Property();
        $property->setSiteId(1593846);
        $property->setPropertyName("Charlton Arms");
        $property->setPropertyType("Townhouse Condo");
        $property->setPropertyStatus("Active");
        $property->setNumUnits(5);
        $property->setNeighbourhoodName("Sutherland");
        $property->setNeighbourhoodId("O48");

        //create an address for the property
        $address = new Address();
        $address->setStreetAddress("12 15th st east");
        $address->setPostalCode("S0E 1A0");
        $address->setCity("Saskatoon");
        $address->setProvince("Saskatchewan");
        $address->setCountry("Canada");
        $property->setAddress($address);

        //create a communication
        $comm = new Communication();
        $comm->setType("In Person");
        $comm->setMedium("Incoming");
        $comm->setContactName("John Smith");
        $comm->setContactEmail("email@email.com");
        $comm->setContactPhone("306-123-4567");
        $comm->setProperty($property);
        $comm->setCategory("Container");
        $comm->setDescription("Bin will be moved to the eastern side of the building");

        //create the client
        $client = static::createClient(array(), array('PHP_AUTH_USER' => 'admin', 'PHP_AUTH_PW'   => 'password'));

        //get the entity manager and make sure the communication exists
        $em = $client->getContainer()->get('doctrine.orm.entity_manager');
        $repo = $em->getRepository(Communication::class);

        //insert the communication
        $commId = $repo->insert($comm);

        $crawler = $client->request("GET","/communication/$commId");

        $response = $client->getResponse()->getContent();

        //check that the page contains all the information from the object
        $this->assertContains("In Person",$response);
        $this->assertContains("Incoming",$response);
        $this->assertContains("John Smith",$response);
        $this->assertContains("email@email.com",$response);
        $this->assertContains("306-123-4567",$response);
        $this->assertContains("Container",$response);
        $this->assertContains("Bin will be moved to the eastern side of the building",$response);
        $this->assertContains("12 15th st east",$response);
    }

    /**
     * Story 11b
     * Tests that if an invalid ID is put in the request that it will fail
     */
    public function testViewBadId(){
        //create a client to get to the page
        $client = static::createClient(array(), array('PHP_AUTH_USER' => 'admin', 'PHP_AUTH_PW'   => 'password'));

        //request the communication view page for a communication that does not exist
        $crawler = $client->request("GET","communication/-5");

        //assert that the correct error message appeared
        $this->assertContains("The specified communication ID could not be found", $client->getResponse()->getContent());
    }

    /**
     * Story 11b
     * Tests that if no ID is put in the request there will be an error message
     */
    public function testViewNoID(){
        //create a client to get to the page
        $client = static::createClient(array(), array('PHP_AUTH_USER' => 'admin', 'PHP_AUTH_PW'   => 'password'));

        //request the communication view page for a communication that does not exist
        $crawler = $client->request("GET","communication/");

        //assert that the correct error message appeared
        $this->assertContains("No communication ID specified", $client->getResponse()->getContent());
    }

    /**
     * Story 11c
     * Test that special characters can be entered into the database
     */
    public function testSearchSpecialCharactersSuccess()
    {
        // get a repository so we can query for data
        $repository = $this->em->getRepository(Communication::class);

        // create a client so we can view the page
        $client = static::createClient(array(), array('PHP_AUTH_USER' => 'admin', 'PHP_AUTH_PW'   => 'password'));

        //// create a communication to search for in the test
        //$communication = new Communication();
        //$communication->setDate("2018-01-01");
        //$communication->setType("Phone");
        //$communication->setMedium("Incoming");
        //$communication->setCategory("Multi-purpose");
        //$communication->setDescription("Its a bin");

        //$repository->insert($communication);

        // go to the page and search for 'Jim'
        $client->request('GET', '/communication/jsonsearch/Multi-purpose');

        // create an array so we can call the search
        $queryStrings = array();
        $queryStrings[] = 'Multi-purpose';

        // query the database
        $repository->communicationSearch($queryStrings);

        // assert that what we expect is actually returned
        $this->assertContains('[{"id":1,"date":"2018-01-01","type":"Phone","medium":"Incoming","contactName":null,"contactEmail":null,"contactPhone":null,"category":"Multi-purpose","description":"Its a bin"}]', $client->getResponse()->getContent());
    }

    /**
     * Story 11c
     * test that the query to search on is too long
     */
    public function testQueryTooLong()
    {
        // create a client so we can view the page
        $client = static::createClient(array(), array('PHP_AUTH_USER' => 'admin', 'PHP_AUTH_PW'   => 'password'));

        // go to the page and search for a string that is 501 characters long
        $client->request('GET', '/Communication/jsonsearch/BobJonesBobJonesBobJonesBobJonesBobJonesBobJonesBobJonesBobJonesBobJonesBobJonesBobJonesBobJonesBobJBobJonesBobJonesBobJonesBobJonesBobJonesBobJonesBobJonesBobJonesBobJonesBobJonesBobJonesBobJonesBobJBobJonesBobJonesBobJonesBobJonesBobJonesBobJonesBobJonesBobJonesBobJonesBobJonesBobJonesBobJonesBobJBobJonesBobJonesBobJonesBobJonesBobJonesBobJonesBobJonesBobJonesBobJonesBobJonesBobJonesBobJonesBobJBobJonesBobJonesBobJonesBobJonesBobJonesBobJonesBobJonesBobJonesBobJonesBobJonesBobJonesBobJonesBobJo');

        // assert that what we expect is actually returned
        $this->assertContains('[]', $client->getResponse()->getContent());
    }

    /**
     * Story 11c
     * test that the query to search on is empty
     */
    public function testQueryEmpty()
    {
        // create a client so we can view the page
        $client = static::createClient(array(), array('PHP_AUTH_USER' => 'admin', 'PHP_AUTH_PW'   => 'password'));

        // go to the page and search for a string that is empty
        $client->request('GET', '/Communication/jsonsearch/');

        // assert that what we expect is actually returned
        $this->assertContains('[]', $client->getResponse()->getContent());
    }

    /**
     * Story 11d
     * Tests that you can edit a communication
     */
    public function testEditSubmitRedirect()
    {
        //Create the client
        $client = static::createClient(array(), array('PHP_AUTH_USER' => 'admin', 'PHP_AUTH_PW'   => 'password'));

        $client->followRedirects(true);

        //Request the communication edit page
        $crawler = $client->request('GET','/communication/1/edit');

        // Get the form
        $form = $crawler->selectButton("Save")->form();

        // Change something
        $form['appbundle_communication[description]'] = "The description of this communication has been changed";

        // Submit the form
        $crawler = $client->submit($form);

        // Assert redirection
        $this->assertContains('/communication/1', $client->getRequest()->getUri());

        $this->assertContains('The description of this communication has been changed', $client->getResponse()->getContent());
    }

    protected function tearDown()
    {
        parent::tearDown();

        // Delete all the things that were just inserted. Or literally everything.
        $client = static::createClient(array(), array('PHP_AUTH_USER' => 'admin', 'PHP_AUTH_PW'   => 'password'));
        $em = $client->getContainer()->get('doctrine.orm.entity_manager');
        $stmt = $em->getConnection()->prepare('DELETE FROM Communication');
        $stmt->execute();
        $stmt = $em->getConnection()->prepare('DELETE FROM Property');
        $stmt->execute();
        $stmt = $em->getConnection()->prepare('DELETE FROM Address');
        $stmt->execute();
        $stmt = $em->getConnection()->prepare('DELETE FROM User');
        $stmt->execute();
        $em->close();

    }
}