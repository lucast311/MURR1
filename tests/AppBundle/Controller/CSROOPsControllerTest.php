<?php

namespace tests\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * CSROOPsControllerTest short summary.
 *
 * CSROOPsControllerTest description.
 *
 * @version 1.0
 * @author cst201
 */
class CSROOPsControllerTest extends WebTestCase
{
    public function testNewActionSuccess()
    {
        $client = static::createClient();
        //Create a client to go through the web page
        //Reques the contact add page
        
        $crawler = $client->request('GET','/oops/add');
        
        $form = $crawler->selectButton('save_form')->form();
        $form['oops[binSerial]'] = 'testOOPs66';
        $form['oops[problemType]'] = 'Damage';
        $form['oops[status]'] = 'In Progress';
        $form['oops[description]'] = 'test oops description';
        $form['oops[image]'] = 'N;';

        
        $crawler = $client->submit($form);
        $this->assertGreaterThan(
            0,
            $crawler->filter('html:contains("OOPs Form Success")')->count()
            );
    }

    /*
    public function testAddActionFailurefName()
    {
        //Create a client to go through the web page
        $client = static::createClient();
        //Reques the contact add page
        $crawler = $client->request('GET','/oops/add');
        $form = $crawler->selectButton('save_form')->form();
        $form['oops[binSerial]'] = 'testOOPs66';
        $form['oops[problemType]'] = 'Damage';
        $form['oops[status]'] = 'test oops status';
        $form['oops[description]'] = 'test oops description';
        $form['oops[image]'] = 'N;';
        //crawler submits the form
        $crawler = $client->submit($form);
        //check for the success message
        $this->assertGreaterThan(
            0,
            $crawler->filter('html:contains("First name cannot be left blank")')->count()
            );


    }

    
    public function testAddActionFailurelName()
    {
        //Create a client to go through the web page
        $client = static::createClient();
        //Reques the contact add page
        $crawler = $client->request('GET','/oops/add');
        $form = $crawler->selectButton('save_form')->form();
        $form['oops[binSerial]'] = 'testOOPs66';
        $form['oops[problemType]'] = 'Damage';
        $form['oops[status]'] = 'test oops status';
        $form['oops[description]'] = 'test oops description';
        $form['oops[image]'] = 'N;';
        //crawler submits the form
        $crawler = $client->submit($form);
        //check for the success message
        $this->assertGreaterThan(
            0,
            $crawler->filter('html:contains("Last name cannot be left blank")')->count()
            );


    }

    public function testAddActionFailureEmailAddress()
    {
        //Create a client to go through the web page
        $client = static::createClient();
        //Reques the contact add page
        $crawler = $client->request('GET','/oops/add');
        $form = $crawler->selectButton('save_form')->form();
        $form['oops[binSerial]'] = 'testOOPs66';
        $form['oops[problemType]'] = 'Damage';
        $form['oops[status]'] = 'test oops status';
        $form['oops[description]'] = 'test oops description';
        $form['oops[image]'] = 'N;';
        //crawler submits the form
        $crawler = $client->submit($form);
        //check for the success message
        $this->assertGreaterThan(
            0,
            $crawler->filter('html:contains("Email address cannot be left blank")')->count()
            );


    }

    public function testAddActionFailurePostalCode()
    {
        //Create a client to go through the web page
        $client = static::createClient();
        //Reques the contact add page
        $crawler = $client->request('GET','/oops/add');
        $form = $crawler->selectButton('save_form')->form();
        $form['oops[binSerial]'] = 'testOOPs66';
        $form['oops[problemType]'] = 'Damage';
        $form['oops[status]'] = 'test oops status';
        $form['oops[description]'] = 'test oops description';
        $form['oops[image]'] = 'N;';
        //crawler submits the form
        $crawler = $client->submit($form);
        //check for the success message
        $this->assertGreaterThan(
            0,
            $crawler->filter('html:contains("Postal code cannot be left blank")')->count()
            );


    }


    public function testAddActionFailureProvince()
    {
        //Create a client to go through the web page
        $client = static::createClient();
        //Reques the contact add page
        $crawler = $client->request('GET','/oops/add');
        $form = $crawler->selectButton('save_form')->form();
        $form['oops[binSerial]'] = 'testOOPs66';
        $form['oops[problemType]'] = 'Damage';
        $form['oops[status]'] = 'test oops status';
        $form['oops[description]'] = 'test oops description';
        $form['oops[image]'] = 'N;';
        //crawler submits the form
        $crawler = $client->submit($form);
        //check for the success message
        $this->assertGreaterThan(
            0,
            $crawler->filter('html:contains("Province cannot be left blank")')->count()
            );


    }


    public function testAddActionFailureCountry()
    {
        //Create a client to go through the web page
        $client = static::createClient();
        //Reques the contact add page
        $crawler = $client->request('GET','/oops/add');
        $form = $crawler->selectButton('save_form')->form();
        $form['oops[binSerial]'] = 'testOOPs66';
        $form['oops[problemType]'] = 'Damage';
        $form['oops[status]'] = 'test oops status';
        $form['oops[description]'] = 'test oops description';
        $form['oops[image]'] = 'N;';
        $crawler = $client->submit($form);
        //check for the success message
        $this->assertGreaterThan(
            0,
            $crawler->filter('html:contains("Country cannot be left blank")')->count()
            );
    }
    */

}