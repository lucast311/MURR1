<?php
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use AppBundle\Entity\Truck;
use Tests\AppBundle\DatabasePrimer;

/**
 * Tests for the Truck repository
 *
 * @version 1.0
 * @author cst206
 */
class TruckRepositoryTest extends KernelTestCase
{
    /**
     * The entity manager
     * @var \Doctrine\ORM\EntityManager
     */
    private $em;
    private $truck;

    public static function setUpBeforeClass()
    {
        self::bootKernel();
        DatabasePrimer::prime(self::$kernel);
    }


    /**
     * Just some setup stuff required by symfony for testing Repositories
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
    }

    /**
     * Story 40a
     * Tests saving a valid truck to the database
     */
    public function testSave()
    {
        $id = $this->truck->getId();
        $this->assertNull($id);
        // Call insert on the repository for the Truck
        $this->em->persist($this->truck);
        $this->em->flush();

        $id = $this->truck->getId();
        // Assert that the id was set
        $this->assertNotNull($id);

        // Get the repository for the Truck
        $repository = $this->em->getRepository(Truck::class);

        $dbTruck = $repository->findById($id)[0];
        $this->assertNotNull($dbTruck);

        // scheck the Trucks id is the same as the returned id
        $this->assertEquals($dbTruck->getId(), $id);
    }

    /**
     * Story 40a
     * Tests updating a truck in the database
     */
    public function testUpdate()
    {
        //Call insert on the repository for the Truck
        $this->em->persist($this->truck);
        $this->em->flush();

        //change truckId
        $newTruckId = "64920";
        $this->truck->setTruckId($newTruckId);
        $this->em->persist($this->truck);
        $this->em->flush();

        //Get the repository for the Truck
        $repository = $this->em->getRepository(Truck::class);

        //make sure new truckId exists in DB
        $this->assertNotNull($repository->findOneBy(array('truckId' => '64920')));
    }

    /**
     * Story 40a
     * Tests that a Truck can be removed
     */
    public function testRemove()
    {
        //Get the truck's database id
        $id = $this->em->persist($this->truck);
        $this->em->flush();
        
        //Now remove the truck
        $this->em->remove($this->truck);
        $this->em->flush();

        //Get the repository for the Truck
        $repository = $this->em->getRepository(Truck::class);

        //make sure that the truck could not be found in the database now
        $this->assertNull($repository->findOneById($id));
    }

    /**
     * (@inheritDoc)
     */
    protected function tearDown()
    {
        parent::tearDown();

        // Delete everything out of the truck table after inserting stuff
        $stmt = $this->em->getConnection()->prepare("DELETE FROM Truck");
        $stmt->execute();

        $this->em->close();
        $this->em = null;//avoid memory meaks
    }
}