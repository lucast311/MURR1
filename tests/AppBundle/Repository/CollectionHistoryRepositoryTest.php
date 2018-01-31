<?php
namespace Tests\AppBundle\Repository;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use AppBundle\Entity\CollectionHistory;
/**
 * CollectionHistoryRepositoryTest short summary.
 *
 * CollectionHistoryRepositoryTest description.
 *
 * @version 1.0
 * @author Dan
 */
class CollectionHistoryRepositoryTest extends KernelTestCase
{
    private $em;

    protected function setUp()
    {
        self::bootKernel();

        $this->em = static::$kernel->getContainer()
            ->get('doctrine')
            ->getManager();

    }

    /**
     * 18a - tests the save function works
     */
    public function testSave()
    {
        $collectionHistory = new CollectionHistory();
        $collectionHistory->setContainerSerial("testSerialRepo");
        $collectionHistory->setType("Bin");
        $collectionHistory->setSize("6");
        $collectionHistory->setStatus("Active");

        $repo = $this->em->getRepository(CollectionHistory::class);

        $id = $repo->save($collectionHistory);
        $this->assertNotNull($id);
        $this->assertEquals($collectionHistory->getId(), $id);

        $repo->remove($collectionHistory);

    }
}