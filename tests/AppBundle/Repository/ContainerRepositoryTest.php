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
        $container->setContainerSerial("testSerial"); 
        $container->setType("Bin"); 
        $container->setSize("6"); 
        $container->setFrequency("Weekly");
        $container->setStatus("Active");

        $repo = $this->em->getRepository(Container::class); 

        $id = $repo->save($container); 
        $this->assertNotNull($id); 
        $this->assertEquals($container->getId(), $id); 
    }


}