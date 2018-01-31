<?php
namespace Tests\AppBundle\Repository;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use AppBundle\Entity\CollectionHistory;

use AppBundle\Entity\Container;

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

        $container = new Container();
        $container->setContainerSerial('18aTestRepo' . time());
        $container->setSize('6 yds');
        $container->setType('bin');
        $container->setStatus('Active'); 

        $collectionHistory = new CollectionHistory();
        $collectionHistory->setContainerId($container->getId());
        $collectionHistory->setNotCollected(false);
        $collectionHistory->setNotes("Success");

        $repo = $this->em->getRepository(CollectionHistory::class);

        $id = $repo->save($collectionHistory);
        $this->assertNotNull($id);
        $this->assertEquals($collectionHistory->getId(), $id);

        $repo->remove($collectionHistory);

    }
}