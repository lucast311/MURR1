<?php

namespace Tests\AppBundle\Repository;

use AppBundle\Entity\OOPs;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Tests\AppBundle\DatabasePrimer;

/**
 * CSROOPsRepositoryTest short summary.
 *
 * CSROOPsRepositoryTest description.
 *
 * @version 1.0
 * @author cst201
 */
class CSROOPsRepositoryTest extends KernelTestCase
{
    //stores the doctrine entity manager
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $em;

    public static function setUpBeforeClass()
    {
        self::bootKernel();
        DatabasePrimer::prime(self::$kernel);
    }


    //set up function, gets the entity manager
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
        //create an object to be inserted
        $testOOPs = new OOPs("1111111111", "damage");

        //call the insert method
        $id = $this->em->getRepository('AppBundle:OOPs')->insert($testOOPs);

        $this->assertNotNull($id); //assert that an ID was returned
        $this->assertEquals($testOOPs->getId(),$id); //check that the returned ID is the same as the object's new ID
    }

    //closes the entity manager
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