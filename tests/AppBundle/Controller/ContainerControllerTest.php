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
        $form['Container[containerSerial]'] = 'testSerial';
        $form['Container[frequency]'] = 'Weekly';
        $form['Container[locationDesc]'] = 'Near backdoor';
        $form['Container[type]'] = 'Bin';
        $form['Container[size]'] = '6';
        $form['Container[long]'] = '10';
        $form['Container[lat]'] = '25';
        $form['Container[status]'] = 'Active';
        $form['Container[reasonForStatus]'] = 'Test reason';
        $form['Container[structureId]'] = '1';
        $form['Container[augmentation]'] = 'Wheels';

        //crawler submits the form
        $crawler = $client->submit($form);
        //check for the success message
        //$this->assertGreaterThan(
        //    0,
        //    $crawler->filter('html:contains("Contact has been successfully added")')->count()
        //    );
        $this->assertContains('Redirecting to /container', $client->getResponse()->getContent());

    }
}