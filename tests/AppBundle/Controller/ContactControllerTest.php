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
        $form = $crawler->selectButton('Save')->form();
        $form['contact[firstName]'] = 'Bob';
        $form['contact[lastName]'] = 'frank';
        $form['contact[organization]'] = 'Murr';
        $form['contact[primaryPhone]'] = '306-921-3344';
        $form['contact[phoneExtention]'] = '';
        $form['contact[secondaryPhone]'] = '';
        $form['contact[emailAddress]'] = 'murr123@gmail.com';
        $form['contact[fax]'] = '';
        $form['contact[address][streetAddress]'] = '123 Main Street';
        $form['contact[address][postalCode]'] = 'S7N 0R7';
        $form['contact[address][city]'] = 'Saskatoon';
        $form['contact[address][province]'] = 'Saskatchewan';
        $form['contact[address][country]'] = 'Canada';
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
        $form = $crawler->selectButton('Save')->form();
        $form['contact[firstName]'] = '';
        $form['contact[lastName]'] = 'frank';
        $form['contact[organization]'] = 'Murr';
        $form['contact[primaryPhone]'] = '306-921-3344';
        $form['contact[phoneExtention]'] = '';
        $form['contact[secondaryPhone]'] = '';
        $form['contact[emailAddress]'] = 'murr123@gmail.com';
        $form['contact[fax]'] = '';
        $form['contact[address][streetAddress]'] = '123 Main Street';
        $form['contact[address][postalCode]'] = 'S7N 0R7';
        $form['contact[address][city]'] = 'Saskatoon';
        $form['contact[address][province]'] = 'Saskatchewan';
        $form['contact[address][country]'] = 'Canada';
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
        $crawler = $client->request('GET','/contact/add');
        //select the form and add values to it.
        $form = $crawler->selectButton('Save')->form();
        $form['contact[firstName]'] = 'Bob';
        $form['contact[lastName]'] = '';
        $form['contact[organization]'] = 'Murr';
        $form['contact[primaryPhone]'] = '306-921-3344';
        $form['contact[phoneExtention]'] = '';
        $form['contact[secondaryPhone]'] = '';
        $form['contact[emailAddress]'] = 'murr123@gmail.com';
        $form['contact[fax]'] = '';
        $form['contact[address][streetAddress]'] = '123 Main Street';
        $form['contact[address][postalCode]'] = 'S7N 0R7';
        $form['contact[address][city]'] = 'Saskatoon';
        $form['contact[address][province]'] = 'Saskatchewan';
        $form['contact[address][country]'] = 'Canada';
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
        $crawler = $client->request('GET','/contact/add');
        //select the form and add values to it.
        $form = $crawler->selectButton('Save')->form();
        $form['contact[firstName]'] = 'Bob';
        $form['contact[lastName]'] = 'Frank';
        $form['contact[organization]'] = 'Murr';
        $form['contact[primaryPhone]'] = '306-921-3344';
        $form['contact[phoneExtention]'] = '';
        $form['contact[secondaryPhone]'] = '';
        $form['contact[emailAddress]'] = '';
        $form['contact[fax]'] = '';
        $form['contact[address][streetAddress]'] = '123 Main Street';
        $form['contact[address][postalCode]'] = 'S7N 0R7';
        $form['contact[address][city]'] = 'Saskatoon';
        $form['contact[address][province]'] = 'Saskatchewan';
        $form['contact[address][country]'] = 'Canada';
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
        $form = $crawler->selectButton('Save')->form();
        $form['contact[firstName]'] = 'Bob';
        $form['contact[lastName]'] = 'Frank';
        $form['contact[organization]'] = 'Murr';
        $form['contact[primaryPhone]'] = '306-921-3344';
        $form['contact[phoneExtention]'] = '';
        $form['contact[secondaryPhone]'] = '';
        $form['contact[emailAddress]'] = 'murr123@gmail.com';
        $form['contact[fax]'] = '';
        $form['contact[address][streetAddress]'] = '123 Main Street';
        $form['contact[address][postalCode]'] = '';
        $form['contact[address][city]'] = 'Saskatoon';
        $form['contact[address][province]'] = 'Saskatchewan';
        $form['contact[address][country]'] = 'Canada';
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
        $crawler = $client->request('GET','/contact/add');
        //select the form and add values to it.
        $form = $crawler->selectButton('Save')->form();
        $form['contact[firstName]'] = 'Bob';
        $form['contact[lastName]'] = 'Frank';
        $form['contact[organization]'] = 'Murr';
        $form['contact[primaryPhone]'] = '306-921-3344';
        $form['contact[phoneExtention]'] = '';
        $form['contact[secondaryPhone]'] = '';
        $form['contact[emailAddress]'] = 'murr123@gmail.com';
        $form['contact[fax]'] = '';
        $form['contact[address][streetAddress]'] = '123 Main Street';
        $form['contact[address][postalCode]'] = 'S7N 0R7';
        $form['contact[address][city]'] = 'Saskatoon';
        $form['contact[address][province]'] = '';
        $form['contact[address][country]'] = 'Canada';
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
        $form = $crawler->selectButton('Save')->form();
        $form['contact[firstName]'] = 'Bob';
        $form['contact[lastName]'] = 'Frank';
        $form['contact[organization]'] = 'Murr';
        $form['contact[primaryPhone]'] = '306-921-3344';
        $form['contact[phoneExtention]'] = '';
        $form['contact[secondaryPhone]'] = '';
        $form['contact[emailAddress]'] = 'murr123@gmail.com';
        $form['contact[fax]'] = '';
        $form['contact[address][streetAddress]'] = '123 Main Street';
        $form['contact[address][postalCode]'] = 'S7N 0R7';
        $form['contact[address][city]'] = 'Saskatoon';
        $form['contact[address][province]'] = 'Saskatchewan';
        $form['contact[address][country]'] = '';
        //crawler submits the form
        $crawler = $client->submit($form);
        //check for the success message
        $this->assertGreaterThan(
            0,
            $crawler->filter('html:contains("Country cannot be left blank")')->count()
            );
    }





}