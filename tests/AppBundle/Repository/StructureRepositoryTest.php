<?php

namespace Tests\AppBundle\Repository;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use AppBundle\Entity\Structure;

/**
 * StructureRepositoryTest short summary.
 *
 * StructureRepositoryTest description.
 *
 * @version 1.0
 * @author cst201
 */
class StructureRepositoryTest extends KernelTestCase
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
        $structure = new Structure();
        
        $repo = $this->em->getRepository(Structure::class);

        $id = $repo->save($structure);
        $this->assertNotNull($id);
        $this->assertEquals($structure->getId(), $id);

        $repo->remove(Structure);

    }
}