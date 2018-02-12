<?php

namespace Tests\AppBundle\Repository;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use AppBundle\Entity\Container;

/**
 * ContainerRepositoryTest short summary.
 *
 * ContainerRepositoryTest description.
 *
 * @version 1.0
 * @author cst201
 */
class ContainerRepositoryTest extends KernelTestCase
{
    private $em;

    protected function setUp()
    {
        self::bootKernel();

        $this->em = static::$kernel->getContainer()
            ->get('doctrine')
            ->getManager();

        

    }

    public function testSave()
    {
        $container = new Container(); 
        $container->setContainerSerial("testSerialRepo"); 
        $container->setType("Bin"); 
        $container->setSize("6");
        $container->setStatus("Active");

        $repo = $this->em->getRepository(Container::class); 

        $id = $repo->save($container); 
        $this->assertNotNull($id); 
        $this->assertEquals($container->getId(), $id); 

        $repo->remove($container); 
    }

     /**
     * (@inheritDoc)
     */
    /*
    protected function tearDown()
    {
        parent::tearDown();

        // Delete everything out of the property table after inserting stuff
        $stmt = $this->em->getConnection()->prepare('DELETE * FROM Container');
        $stmt->execute();

        $this->em->close();
        $this->em = null; //avoid memory meaks
    }*/

}