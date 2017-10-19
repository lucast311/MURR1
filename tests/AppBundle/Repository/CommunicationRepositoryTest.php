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


    public function testInsert()
    {
        //create a communication
        $comm = new Communication();
        $comm->__set("date","2017-10-05");
        $comm->__set("type", "phone");
        $comm->__set("medium", "incoming");
        $comm->__set("contact", 1);
        $comm->__set("property", 1);
        $comm->__set("category","Container");
        $comm->__set("description","Container has graffiti and needs to be cleaned. Action request made");

        //add to database and return the ID
        $id = $this->em->getRepository(Communication::class)
            ->addToDatabase($comm);

        $this->assertEquals($id,$comm->__get('id'));
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