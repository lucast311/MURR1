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
        $form = $crawler->selectButton('submit')->from();
        $from['firstName'] = 'Bob';
        $from['lastName'] = 'frank';
        $from['Organization'] = 'Murr';
        $from['officePhone'] = '3069213344';
        $from['phoneExtention'] = '';
        $from['mobilePhone'] = '';
        $from['emailAddress'] = 'murr123@gmail.com';
        $from['fax'] = '';
        $from['postalCode'] = 'S7N0R7';
        $from['city'] = 'Saskatoon';
        $from['province'] = 'Saskatchewan';
        $from['country'] = 'Canada';
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
        $form = $crawler->selectButton('submit')->from();
        $from['firstName'] = '';
        $from['lastName'] = 'frank';
        $from['Organization'] = 'Murr';
        $from['officePhone'] = '3069213344';
        $from['phoneExtention'] = '';
        $from['mobilePhone'] = '';
        $from['emailAddress'] = 'murr123@gmail.com';
        $from['fax'] = '';
        $from['postalCode'] = 'S7N0R7';
        $from['city'] = 'Saskatoon';
        $from['province'] = 'Saskatchewan';
        $from['country'] = 'Canada';
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
        $form = $crawler->selectButton('submit')->from();
        $from['firstName'] = 'Bob';
        $from['lastName'] = '';
        $from['Organization'] = 'Murr';
        $from['officePhone'] = '3069213344';
        $from['phoneExtention'] = '';
        $from['mobilePhone'] = '';
        $from['emailAddress'] = 'murr123@gmail.com';
        $from['fax'] = '';
        $from['postalCode'] = 'S7N0R7';
        $from['city'] = 'Saskatoon';
        $from['province'] = 'Saskatchewan';
        $from['country'] = 'Canada';
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
        $form = $crawler->selectButton('submit')->from();
        $from['firstName'] = 'Bob';
        $from['lastName'] = 'Frank';
        $from['Organization'] = 'Murr';
        $from['officePhone'] = '3069213344';
        $from['phoneExtention'] = '';
        $from['mobilePhone'] = '';
        $from['emailAddress'] = '';
        $from['fax'] = '';
        $from['postalCode'] = 'S7N0R7';
        $from['city'] = 'Saskatoon';
        $from['province'] = 'Saskatchewan';
        $from['country'] = 'Canada';
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
        $form = $crawler->selectButton('submit')->from();
        $from['firstName'] = 'Bob';
        $from['lastName'] = 'Frank';
        $from['Organization'] = 'Murr';
        $from['officePhone'] = '3069213344';
        $from['phoneExtention'] = '';
        $from['mobilePhone'] = '';
        $from['emailAddress'] = 'murr123@gmail.com';
        $from['fax'] = '';
        $from['postalCode'] = '';
        $from['city'] = 'Saskatoon';
        $from['province'] = 'Saskatchewan';
        $from['country'] = 'Canada';
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
        $form = $crawler->selectButton('submit')->from();
        $from['firstName'] = 'Bob';
        $from['lastName'] = 'Frank';
        $from['Organization'] = 'Murr';
        $from['officePhone'] = '3069213344';
        $from['phoneExtention'] = '';
        $from['mobilePhone'] = '';
        $from['emailAddress'] = 'murr123@gmail.com';
        $from['fax'] = '';
        $from['postalCode'] = 'S7N0R7';
        $from['city'] = 'Saskatoon';
        $from['province'] = '';
        $from['country'] = 'Canada';
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
        $form = $crawler->selectButton('submit')->from();
        $from['firstName'] = 'Bob';
        $from['lastName'] = 'Frank';
        $from['Organization'] = 'Murr';
        $from['officePhone'] = '3069213344';
        $from['phoneExtention'] = '';
        $from['mobilePhone'] = '';
        $from['emailAddress'] = 'murr123@gmail.com';
        $from['fax'] = '';
        $from['postalCode'] = 'S7N0R7';
        $from['city'] = 'Saskatoon';
        $from['province'] = 'Saskatchewan';
        $from['country'] = '';
        //crawler submits the form
        $crawler = $client->submit($form);
        //check for the success message
        $this->assertGreaterThan(
            0,
            $crawler->filter('html:contains("Country cannot be left blank")')->count()
            );


    }



}