<?php

namespace Tests\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
//use Doctrine\Common\Persistence\ObjectRepository;

class CommunicationControllerTest extends WebTestCase
{
    public function testFormSuccess()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/communication/submit');

        $form = $crawler->selectButton('submit')->form();

        //set form values
        $form['form[date]'] = "2017-10-05";
        $form['form[type]']="Phone";
        $form['form[medium]']="incoming";
        $form['form[contact]']=1; //contact id
        $form['form[property]']=1; //property id
        $form['form[category]']="Container";
        $form["form[description]"]="Container has graffiti and needs to be cleaned. Action request made";

        $crawler = $client->submit($form);

        $this->assertContains("Communication added successfully",$client->getResponse()->getContent());
    }

    public function testFutureDate()
    {

        $client = static::createClient();

        $crawler = $client->request('GET', '/communication/submit');

        $form = $crawler->selectButton('submit')->form();

        //set form values
        $form['form[date]'] = "2019-10-05";

        $crawler = $client->submit($form);

        assertContains("Please select a current or past date",$client->getResponse()->getContent());
    }

    public function testEmptyDate()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/communication/submit');

        $form = $crawler->selectButton('submit')->form();

        //set form values
        $form['form[date]'] = "";

        $crawler = $client->submit($form);

        assertContains("Please select a date",$client->getResponse()->getContent());
    }

    public function testNonExistantDate()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/communication/submit');

        $form = $crawler->selectButton('submit')->form();

        //set form values
        $form['form[date]'] = "2017-2-30";

        $crawler = $client->submit($form);

        assertContains("Please select a valid date",$client->getResponse()->getContent());
    }

    public function testNoType()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/communication/submit');

        $form = $crawler->selectButton('submit')->form();

        //set form values
        $form['form[type]']=0;

        $crawler = $client->submit($form);

        assertContains("Please select a type of communication",$client->getResponse()->getContent());
    }

    public function testNoMedium()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/communication/submit');

        $form = $crawler->selectButton('submit')->form();

        //set form values
        $form['form[medium]']=0;

        $crawler = $client->submit($form);

        assertContains("Please select incoming or outgoing",$client->getResponse()->getContent());
    }

    public function testBlankContact()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/communication/submit');

        $form = $crawler->selectButton('submit')->form();

        //set form values
        $form['form[contact]']=0; //blank contact ID

        $crawler = $client->submit($form);

        assertContains("Please enter a contact",$client->getResponse()->getContent());
    }

    public function testResidentContact()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/communication/submit');

        $form = $crawler->selectButton('submit')->form();

        //set form values
        $form['form[date]'] = "2017-10-05";
        $form['form[type]']="Phone";
        $form['form[medium]']="incoming";
        $form['form[contact]']=-1; //identifier for a resident, will not be stored
        $form['form[property]']=1;
        $form['form[category]']="Container";
        $form["form[description]"]="Container has graffiti and needs to be cleaned. Action request made";

        $crawler = $client->submit($form);

        $this->assertContains("Communication added successfully",$client->getResponse()->getContent());
    }

    public function testBlankProperty()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/communication/submit');

        $form = $crawler->selectButton('submit')->form();

        //set form values
        $form['form[property]']=0; //blank property ID

        $crawler = $client->submit($form);

        assertContains("Please select a property",$client->getResponse()->getContent());
    }

    public function testMultiOrNAProperty()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/communication/submit');

        $form = $crawler->selectButton('submit')->form();

        //set form values
        //set form values
        $form['form[date]'] = "2017-10-05";
        $form['form[type]']="Phone";
        $form['form[medium]']="incoming";
        $form['form[contact]']=1; //contact id
        $form['form[property]']=-1; //multi-property or N/A property identifier
        $form['form[category]']="Container";
        $form["form[description]"]="Container has graffiti and needs to be cleaned. Action request made";

        $crawler = $client->submit($form);

        assertContains("Communication added successfully",$client->getResponse()->getContent());
    }

    public function testBlankCategory()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/communication/submit');

        $form = $crawler->selectButton('submit')->form();

        //set form values
        $form['form[category]']=0; //blank category value


        $crawler = $client->submit($form);

        assertContains("Please select a category",$client->getResponse()->getContent());
    }

    public function testBlankDescription()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/communication/submit');

        $form = $crawler->selectButton('submit')->form();

        //set form values
        $form['form[description]']=""; //blank description


        $crawler = $client->submit($form);

        assertContains("Please provide a brief description of the communication",$client->getResponse()->getContent());
    }

    public function testShortDescription()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/communication/submit');

        $form = $crawler->selectButton('submit')->form();

        //set form values
        $form['form[description]']="Talked"; //description too short


        $crawler = $client->submit($form);

        assertContains("Please provide a description of 50 characters or more",$client->getResponse()->getContent());
    }

    public function testLongDescription()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/communication/submit');

        $form = $crawler->selectButton('submit')->form();


        //set form values
        $form['form[description]']=str_repeat('a',2001);//generate a string that is too long


        $crawler = $client->submit($form);

        assertContains("Please keep the description under 2000 characters",$client->getResponse()->getContent());
    }

    //public function testDataBaseEntry()
    //{
    //    $client = static::createClient();

    //    $crawler = $client->request('GET', '/communication/submit');

    //    $form = $crawler->selectButton('submit')->form();

    //    //set form values
    //    $form['form[date]'] = "2017-10-05";
    //    $form['form[type]']="Phone";
    //    $form['form[medium]']="incoming";
    //    $form['form[contact]']=1; //contact id
    //    $form['form[property]']=1; //property id
    //    $form['form[category]']="Container";
    //    $form["form[description]"]="Container has graffiti and needs to be cleaned. Action request made";

    //    $crawler = $client->submit($form);

    //    $communicationRepo = $this->createMock(ObjectRepository::class);

    //    $communicationRepo->expects($this->any())
    //        ->method('find')
    //        ->willReturn()

    //}
}