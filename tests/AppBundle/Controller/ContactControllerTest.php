<?php
namespace Tests\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ContactControllerTest extends WebTestCase
{
    public function testAddActionSuccess()
    {
        //Create a client to go through the web page
        $client = static::createClient();
        //Reques the contact add page
        $crawler = $client->request('GET','/contact/add');
        //select the form and add values to it.
        $form = $crawler->selectButton('submit')->form();
        $form['firstName'] = 'Bob';
        $form['lastName'] = 'frank';
        $form['Organization'] = 'Murr';
        $form['officePhone'] = '3069213344';
        $form['phoneExtention'] = '';
        $form['mobilePhone'] = '';
        $form['emailAddress'] = 'murr123@gmail.com';
        $form['fax'] = '';
        $form['postalCode'] = 'S7N0R7';
        $form['city'] = 'Saskatoon';
        $form['province'] = 'Saskatchewan';
        $form['country'] = 'Canada';
        //crawler submits the form
        $crawler = $client->submit($form);
        //check for the success message
        $this->assertGreaterThan(
            0,
            $crawler->filter('html:contains("Contact has been successfully added")')->count()
            );


    }


    public function testAddActionFailurefName()
    {
        //Create a client to go through the web page
        $client = static::createClient();
        //Reques the contact add page
        $crawler = $client->request('GET','/contact/add');
        //select the form and add values to it.
        $form = $crawler->selectButton('submit')->form();
        $form['firstName'] = '';
        $form['lastName'] = 'frank';
        $form['Organization'] = 'Murr';
        $form['officePhone'] = '3069213344';
        $form['phoneExtention'] = '';
        $form['mobilePhone'] = '';
        $form['emailAddress'] = 'murr123@gmail.com';
        $form['fax'] = '';
        $form['postalCode'] = 'S7N0R7';
        $form['city'] = 'Saskatoon';
        $form['province'] = 'Saskatchewan';
        $form['country'] = 'Canada';
        //crawler submits the form
        $crawler = $client->submit($form);
        //check for the success message
        $this->assertGreaterThan(
            0,
            $crawler->filter('html:contains("First Name cannot be left blank")')->count()
            );


    }

    public function testAddActionFailurelName()
    {
        //Create a client to go through the web page
        $client = static::createClient();
        //Reques the contact add page
        $crawler = $client->request('GET','/contact/add');
        //select the form and add values to it.
        $form = $crawler->selectButton('submit')->form();
        $form['firstName'] = 'Bob';
        $form['lastName'] = '';
        $form['Organization'] = 'Murr';
        $form['officePhone'] = '3069213344';
        $form['phoneExtention'] = '';
        $form['mobilePhone'] = '';
        $form['emailAddress'] = 'murr123@gmail.com';
        $form['fax'] = '';
        $form['postalCode'] = 'S7N0R7';
        $form['city'] = 'Saskatoon';
        $form['province'] = 'Saskatchewan';
        $form['country'] = 'Canada';
        //crawler submits the form
        $crawler = $client->submit($form);
        //check for the success message
        $this->assertGreaterThan(
            0,
            $crawler->filter('html:contains("Last Name cannot be left blank")')->count()
            );


    }

    public function testAddActionFailureEmailAddress()
    {
        //Create a client to go through the web page
        $client = static::createClient();
        //Reques the contact add page
        $crawler = $client->request('GET','/contact/add');
        //select the form and add values to it.
        $form = $crawler->selectButton('submit')->form();
        $form['firstName'] = 'Bob';
        $form['lastName'] = 'Frank';
        $form['Organization'] = 'Murr';
        $form['officePhone'] = '3069213344';
        $form['phoneExtention'] = '';
        $form['mobilePhone'] = '';
        $form['emailAddress'] = '';
        $form['fax'] = '';
        $form['postalCode'] = 'S7N0R7';
        $form['city'] = 'Saskatoon';
        $form['province'] = 'Saskatchewan';
        $form['country'] = 'Canada';
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
        $crawler = $client->request('GET','/contact/add');
        //select the form and add values to it.
        $form = $crawler->selectButton('submit')->form();
        $form['firstName'] = 'Bob';
        $form['lastName'] = 'Frank';
        $form['Organization'] = 'Murr';
        $form['officePhone'] = '3069213344';
        $form['phoneExtention'] = '';
        $form['mobilePhone'] = '';
        $form['emailAddress'] = 'murr123@gmail.com';
        $form['fax'] = '';
        $form['postalCode'] = '';
        $form['city'] = 'Saskatoon';
        $form['province'] = 'Saskatchewan';
        $form['country'] = 'Canada';
        //crawler submits the form
        $crawler = $client->submit($form);
        //check for the success message
        $this->assertGreaterThan(
            0,
            $crawler->filter('html:contains("Postal Code cannot be left blank")')->count()
            );


    }


    public function testAddActionFailureProvince()
    {
        //Create a client to go through the web page
        $client = static::createClient();
        //Reques the contact add page
        $crawler = $client->request('GET','/contact/add');
        //select the form and add values to it.
        $form = $crawler->selectButton('submit')->form();
        $form['firstName'] = 'Bob';
        $form['lastName'] = 'Frank';
        $form['Organization'] = 'Murr';
        $form['officePhone'] = '3069213344';
        $form['phoneExtention'] = '';
        $form['mobilePhone'] = '';
        $form['emailAddress'] = 'murr123@gmail.com';
        $form['fax'] = '';
        $form['postalCode'] = 'S7N0R7';
        $form['city'] = 'Saskatoon';
        $form['province'] = '';
        $form['country'] = 'Canada';
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
        $crawler = $client->request('GET','/contact/add');
        //select the form and add values to it.
        $form = $crawler->selectButton('submit')->form();
        $form['firstName'] = 'Bob';
        $form['lastName'] = 'Frank';
        $form['Organization'] = 'Murr';
        $form['officePhone'] = '3069213344';
        $form['phoneExtention'] = '';
        $form['mobilePhone'] = '';
        $form['emailAddress'] = 'murr123@gmail.com';
        $form['fax'] = '';
        $form['postalCode'] = 'S7N0R7';
        $form['city'] = 'Saskatoon';
        $form['province'] = 'Saskatchewan';
        $form['country'] = '';
        //crawler submits the form
        $crawler = $client->submit($form);
        //check for the success message
        $this->assertGreaterThan(
            0,
            $crawler->filter('html:contains("Country cannot be left blank")')->count()
            );
    }





}