<?php
namespace Tests\AppBundle\Repository;

use AppBundle\Entity\Communication;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use DateTime;

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
        $com = new Communication();
        $com->setDate(new DateTime("2017-10-05"));
        $com->setType("phone");
        $com->setMedium("incoming");
        $com->setContact(1);
        $com->setProperty(1);
        $com->setCategory("container");
        $com->setDescription("Container has graffiti and needs to be cleaned. Action request made");
        $com->setUser(1);

        //add to database and return the ID
        $id = $this->em->getRepository(Communication::class)
            ->insert($com);

        $this->assertEquals($id,$com->getId());
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