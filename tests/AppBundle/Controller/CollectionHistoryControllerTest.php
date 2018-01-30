<?php

namespace  Tests\AppBundle\Controller;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use AppBundle\Entity\CollectionHistory;
use AppBundle\Entity\Container;
/**
	* CollectionHistoryControllerTest short summary.
	*
	* CollectionHistoryControllerTest description.
	*
	* @version 1.0
	* @author cst201
	*/
class CollectionHistoryControllerTest extends WebTestCase
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
     * 18a - adds a collection history
     */
    public function testAddActionSuccess()
    {
        $client = static::createClient();

        $container = new Container();
        $container->setContainerSerial('18aTest');
        $container->setSize('6 yds');

        $containerRepo = $em->getRepository(Container::class);
        $containerRepo->save($container);

        $crawler = $client->request('GET', '/collectionhistory/new');

        $form = $crawler->selectButton('Create')->form();
        $form['appbundle_collectionhistory[containerId]'] = '18aTest';
        $form['appbundle_collectionhistory[notCollected]'] = false;
        $form['appbundle_collectionhistory[notes]'] = 'Collected successfully';

        $crawler = $client->submit($form);
        $this->assertContains('Test1', $client->getResponse()->getContent());
    }
}
