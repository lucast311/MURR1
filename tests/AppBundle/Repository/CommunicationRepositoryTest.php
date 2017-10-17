<?php
namespace Tests\AppBundle\Repository;

use AppBundle\Entity\Communication;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class CommunicationRepositoryTest extends KernelTestCase
{
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $em;

    /**
     * {@inheritDoc}
     */
    protected function setUp()
    {
        self::bootKernel();

        $this->em = static::$kernel->getContainer()
            ->get('doctrine')
            ->getManager();
    }


    public function testAddToDatabase()
    {
        //create a communication
        $comm = new Communication();
        $comm->date = "2017-10-05";
        $comm->type = "phone";
        $comm->medium = "incoming";
        $comm->contact = 1;
        $comm->property = 1;
        $comm->category = "Container";
        $comm->description = "Container has graffiti and needs to be cleaned. Action request made";

        //add to database and return the ID
        $newComm = $this->em->getRepository(Communication::class)
            ->addToDatabase($comm);

        $this->assertEquals($newComm->id,$comm->id);
    }

    /**
     * {@inheritDoc}
     */
    protected function tearDown()
    {
        parent::tearDown();

        $this->em->close();
        $this->em = null; // avoid memory leaks
    }
}