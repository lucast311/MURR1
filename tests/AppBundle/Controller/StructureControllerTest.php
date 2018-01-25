<?php

namespace Tests\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use AppBundle\Entity\Structure;

/**
 * StructureControllerTest short summary.
 *
 * StructureControllerTest description.
 *
 * @version 1.0
 * @author cst201
 */
class StructureControllerTest extends WebTestCase
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
     * 17a - test that I can add a new Structure
     */
    public function testAddActionSuccess()
    {

        //Create a client to go through the web page
        $client = static::createClient();
        //Reques the contact add page
        $crawler = $client->request('GET','/structure/new');
        //select the form and add values to it.
        $form = $crawler->selectButton('Create')->form();
        //$form['appbundle_container[containerSerial]'] = 'testSerialController' + time();
        

        //crawler submits the form
        $crawler = $client->submit($form);
        //check for the success message
        $this->assertContains('Structure', $client->getResponse()->getContent());
    }

}