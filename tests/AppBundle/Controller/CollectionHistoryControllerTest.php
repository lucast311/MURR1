<?php

namespace  Tests\AppBundle\Controller;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use AppBundle\Entity\CollectionHistory;
use AppBundle\Entity\Container;
use AppBundle\DataFixtures\ORM\LoadUserData;
use Tests\AppBundle\DatabasePrimer;
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

    public static function setUpBeforeClass()
    {
        self::bootKernel();
        DatabasePrimer::prime(self::$kernel);
    }


    protected function setUp()
    {
        self::bootKernel();

        $this->em = static::$kernel->getContainer()
            ->get('doctrine')
            ->getManager();

        $encoder = static::$kernel->getContainer()->get('security.password_encoder');

        $userLoader = new LoadUserData($encoder);
        $userLoader->load($this->em);

    }

    /**
     * 18a - adds a collection history
     */
    public function testAddActionSuccess()
    {
        $client = static::createClient(array(), array('PHP_AUTH_USER' => 'admin', 'PHP_AUTH_PW'   => 'password'));

        $container = new Container();
        $container->setContainerSerial('18aTestController' . time());
        $container->setSize('6 yds');
        $container->setType('bin');
        $container->setStatus('Active');

        $containerRepo = $this->em->getRepository(Container::class);
        $containerRepo->save($container);

        $crawler = $client->request('GET', '/collectionhistory/new');

        $form = $crawler->selectButton('Create')->form();

        $form['appbundle_collectionhistory[containerId]'] = 1;
        $form['appbundle_collectionhistory[notCollected]'] = false;
        //var_dump($form['appbundle_collectionhistory']);
        $form['appbundle_collectionhistory[dateCollected][year]'] = '2017';
        $form['appbundle_collectionhistory[dateCollected][month]'] = '1';
        $form['appbundle_collectionhistory[dateCollected][day]'] = '1';
        $form['appbundle_collectionhistory[notes]'] = 'Collected successfully';

        $crawler = $client->submit($form);
        $this->assertContains('Redirecting to /collectionhistory/', $client->getResponse()->getContent());

        $containerRepo->remove($container);
    }

    protected function tearDown()
    {
        parent::tearDown();

        $stmt = $this->em->getConnection()->prepare("DELETE FROM User");
        $stmt->execute();
        $stmt = $this->em->getConnection()->prepare('DELETE FROM collection_history');
        $stmt->execute();

        $this->em->close();
        $this->em = null; //avoid memory meaks
    }
}
