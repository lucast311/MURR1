<?php

namespace Tests\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class DefaultControllerTest extends WebTestCase
{
    public function testFormSuccess()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/communication/submit');

        $form = $crawler->selectButton('submit')->form();

        //set form values
        $form['date'] = "2017-10-05";
        $form['type']="Phone";
        $form['medium']="incoming";
        $form['contact']=1; //contact id
        $form['property']=1; //property id
        $form['category']="Container";
        $form["description"]="Container has graffiti and needs to be cleaned. Action request made";

        $crawler = $client->submit($form);

        $this->assertContains("Communication added successfully",$crawler->filter('.success')->text());
    }

    public function testFutureDate()
    {

        $client = static::createClient();

        $crawler = $client->request('GET', '/communication/submit');

        $form = $crawler->selectButton('submit')->form();

        //set form values
        $form['date'] = "2019-10-05";

        $crawler = $client->submit($form);

        assertContains("Please select a current or past date",$crawler->filter('.error')->text());
    }

    public function testEmptyDate()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/communication/submit');

        $form = $crawler->selectButton('submit')->form();

        //set form values
        $form['date'] = "";

        $crawler = $client->submit($form);

        assertContains("Please select a date",$crawler->filter('.error')->text());
    }

    public function testNonExistantDate()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/communication/submit');

        $form = $crawler->selectButton('submit')->form();

        //set form values
        $form['date'] = "2017-2-30";

        $crawler = $client->submit($form);

        assertContains("Please select a valid date",$crawler->filter('.error')->text());
    }

    public function testNoType()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/communication/submit');

        $form = $crawler->selectButton('submit')->form();

        //set form values
        $form['type']=0;

        $crawler = $client->submit($form);

        assertContains("Please select a type of communication",$crawler->filter('.error')->text());
    }

    public function testNoMedium()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/communication/submit');

        $form = $crawler->selectButton('submit')->form();

        //set form values
        $form['medium']=0;

        $crawler = $client->submit($form);

        assertContains("Please select incoming or outgoing",$crawler->filter('.error')->text());
    }

    public function testBlankContact()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/communication/submit');

        $form = $crawler->selectButton('submit')->form();

        //set form values
        $form['contact']=0; //blank contact ID

        $crawler = $client->submit($form);

        assertContains("Please enter a contact",$crawler->filter('.error')->text());
    }

    public function testResidentContact()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/communication/submit');

        $form = $crawler->selectButton('submit')->form();

        //set form values
        $form['date'] = "2017-10-05";
        $form['type']="Phone";
        $form['medium']="incoming";
        $form['contact']=1; //contact id
        $form['property']=-1; //identifier for a resident, will not be stored
        $form['category']="Container";
        $form["description"]="Container has graffiti and needs to be cleaned. Action request made";

        $crawler = $client->submit($form);

        $this->assertContains("Communication added successfully",$crawler->filter('.success')->text());
    }

    public function testBlankProperty()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/communication/submit');

        $form = $crawler->selectButton('submit')->form();

        //set form values
        $form['property']=0; //blank property ID

        $crawler = $client->submit($form);

        assertContains("Please select a property",$crawler->filter('.error')->text());
    }
}