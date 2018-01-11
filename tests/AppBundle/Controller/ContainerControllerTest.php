<?php


namespace Tests\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use AppBundle\Entity\Container;

/**
 * ContainerControllerTest short summary.
 *
 * ContainerControllerTest description.
 *
 * @version 1.0
 * @author cst201
 */
class ContainerControllerTest extends WebTestCase
{
    private $em;

    protected function setUp()
    {
        self::bootKernel();
        $this->em = static::$kernel->getContainer()
            ->get('doctrine')
            ->getManager();

    }

    public function testAddActionSuccess()
    {
        //Create a client to go through the web page
        $client = static::createClient();
        //Reques the contact add page
        $crawler = $client->request('GET','/container/new');
        //select the form and add values to it.
        $form = $crawler->selectButton('Create')->form();
        $form['appbundle_container[containerSerial]'] = 'testSerial';
        $form['appbundle_container[type]'] = 'bin';
        $form['appbundle_container[size]'] = '6';

        //crawler submits the form
        $crawler = $client->submit($form);
        //check for the success message
        //$this->assertGreaterThan(
        //    0,
        //    $crawler->filter('html:contains("Contact has been successfully added")')->count()
        //    );
        $this->assertContains('Redirecting to /contact', $client->getResponse()->getContent());

    }
}