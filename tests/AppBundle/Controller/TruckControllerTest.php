<?php
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use AppBundle\Entity\Truck;
use AppBundle\DataFixtures\ORM\LoadTruckData;
use Tests\AppBundle\DatabasePrimer;

/**
 * TruckControllerTest
 * tests for the truck controller
 * ***NOTE: ANYTHING THAT SEEMS MISSING FROM HERE IS PROBABLY IN THE MINK TESTS***
 * @version 1.0
 * @author cst206
 */
class TruckControllerTest extends WebTestCase
{
    private $em;
    private $truck;

    public static function setUpBeforeClass()
    {
        self::bootKernel();
        DatabasePrimer::prime(self::$kernel);
    }

    /**
     * Just some setup stuff
     * {@inheritDoc}
     */
    protected function setUp()
    {
        self::bootKernel();

        $this->em = static::$kernel->getContainer()
            ->get('doctrine')
            ->getManager();

        $this->truck = (new Truck())
            ->setTruckId("00886")
            ->setType("Large");

        // Load the admin user into the database so they can log in
        $encoder = static::$kernel->getContainer()->get('security.password_encoder');

        $truckLoader = new LoadTruckData($encoder);
        $truckLoader->load($this->em);
    }


   /**
     * Story 40a
     * Tests that you can view a Truck in the list
     * Also tested in Mink
     */
    public function testViewTruck()
    {
        // Get the truck repository
        $repository = $this->em->getRepository(Truck::class);
        //insert the truck to db
        $repository->save($this->truck);

        // get the client
        $client = static::createClient(array(), array('PHP_AUTH_USER' => 'admin', 'PHP_AUTH_PW'   => 'password'));
        $client->followRedirects(true);

        // go to truck utility page
        $crawler = $client->request('GET', '/trucks');

        // Check that the truck is displayed in the list
        $this->assertContains("00886", $crawler->filter("table")->html());
        $this->assertContains("Large", $crawler->filter("table")->html());
    }

    /**
     * Story 40a
     * Tests that you submit a new truck to the controller and checks the list to ensure it popped up.
     * Also tested in Mink
     */
    public function testAddAndViewTruck()
    {
        // get the client
        $client = static::createClient(array(), array('PHP_AUTH_USER' => 'admin', 'PHP_AUTH_PW'   => 'password'));
        $client->followRedirects(true);
        //go to truck utility page
        $crawler = $client->request('GET', '/trucks');

        // use form to add truck
        $form = $crawler->selectButton('Add')->form();
        $form["appbundle_truck[truckId]"] = $this->truck->getTruckId();
        $form["appbundle_truck[type]"] = $this->truck->getType();
        $crawler = $client->submit($form);

        // check that the table contains the new truck
        $this->assertContains($this->truck->getTruckId(), $crawler->filter("table")->html());
        $this->assertContains($this->truck->getType(), $crawler->filter("table")->html());
    }


    /**
     * (@inheritDoc)
     */
    protected function tearDown()
    {
        parent::tearDown();
        $client = static::createClient();
        $em = $client->getContainer()->get('doctrine.orm.entity_manager');

        // Destroys the trucks in the database
        $stmt = $em->getConnection()->prepare('DELETE FROM Truck');
        $stmt->execute();

        // Destroys the users in the database
        $stmt = $em->getConnection()->prepare('DELETE FROM User');
        $stmt->execute();
    }

}