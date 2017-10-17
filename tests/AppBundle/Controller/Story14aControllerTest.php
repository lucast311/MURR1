<?php
namespace Tests\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class DefaultControllerTest extends WebTestCase
{
    public function testSuccess()
    {
        $client = static::createClient();

        $crawler = $client->request("GET",'/operation/edumat');

        // form
        $form = $crawler->selectButton('submit')->form();

        // Populate form
        $form['name'] = "SchoolDelivery";
        $form['status'] = "Complete";
        $form['dateCreated'] = "10/17/2017";
        $form['dateFinished'] = "10/17/2017";
        $form['description'] = "Deliver stufff to school";
        $form['recipient'] = "Hamburg School";

        // Submit the form to the crawler
        $crawler = $client->submit($form);

        $this->assertCount(0, $crawler->filter('.error'));
    }

    public function testNameLength()
    {
        $client = static::createClient();

        $crawler = $client->request("GET",'/operation/edumat');

        // form
        $form = $crawler->selectButton('submit')->form();

        // Populate form
        $form['name'] = "aaaaaaSchoolDeliveryaaaaaaaaaaa";


        // Submit the form to the crawler
        $crawler = $client->submit($form);

        // Test to see if our error message is on has appeared.
        $this->assertContains("The maximum number of characters for this field is 30.", $client->getResponse()->getContent());
    }

    public function testNameCharacters()
    {
        $client = static::createClient();

        $crawler = $client->request("GET",'/operation/edumat');

        // form
        $form = $crawler->selectButton('submit')->form();

        // Populate form
        $form['name'] = "School Delivery1";

        // Submit the form to the crawler
        $crawler = $client->submit($form);

        // Test to see if our error message is on has appeared.
        $this->assertContains("You have entered invalid characters into the name field. Please use letter names only. Additional characters may be used in the description field.",
                                $client->getResponse()->getContent());
    }

    public function testNameSpaces()
    {
        $client = static::createClient();

        $crawler = $client->request("GET",'/operation/edumat');

        // form
        $form = $crawler->selectButton('submit')->form();

        // Populate form
        $form['name'] = "       ";

        // Submit the form to the crawler
        $crawler = $client->submit($form);

        // Test to see if our error message is on has appeared.
        $this->assertContains("This field is required.", $client->getResponse()->getContent());
    }

    //public function testDescLength()
    //{
    //    $client = static::createClient();

    //    $crawler = $client->request("GET",'/operation/edumat');

    //    // form
    //    $form = $crawler->selectButton('submit')->form();

    //    // Populate form
    //    $form['description'] = "sssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssss";

    //    // Submit the form to the crawler
    //    $crawler = $client->submit($form);

    //    // Test to see if our error message is on has appeared.
    //    $this->assertContains("The description you have entered is 430 characters long. The maximum number of characters for this field is 250.",
    //                            $client->getResponse()->getContent());
    //}

    //public function testRecipientLength()
    //{
    //    $client = static::createClient();

    //    $crawler = $client->request("GET",'/operation/edumat');

    //    // form
    //    $form = $crawler->selectButton('submit')->form();

    //    // Populate form
    //    $form['recipient'] = "sssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssss";

    //    // Submit the form to the crawler
    //    $crawler = $client->submit($form);

    //    // Test to see if our error message is on has appeared.
    //    $this->assertContains("The recipient you have entered is 114 characters long. The maximum number of characters for this field is 50.",
    //                            $client->getResponse()->getContent());
    //}

    public function testRecipientCharacters()
    {
        $client = static::createClient();

        $crawler = $client->request("GET",'/operation/edumat');

        // form
        $form = $crawler->selectButton('submit')->form();

        // Populate form
        $form['recipient'] = "@Saskpolytech";

        // Submit the form to the crawler
        $crawler = $client->submit($form);

        // Test to see if our error message is on has appeared.
        $this->assertContains("You have entered invalid characters into this field. Please use Alpha-numeric (letters/numbers) characters only.", $client->getResponse()->getContent());
    }

    public function testRecipientSpaces()
    {
        $client = static::createClient();

        $crawler = $client->request("GET",'/operation/edumat');

        // form
        $form = $crawler->selectButton('submit')->form();

        // Populate form
        $form['recipient'] = "       ";

        // Submit the form to the crawler
        $crawler = $client->submit($form);

        // Test to see if our error message is on has appeared.
        $this->assertContains("This field is required.", $client->getResponse()->getContent());
    }

    public function testRequired()
    {
        $client = static::createClient();

        $crawler = $client->request("GET",'/operation/edumat');

        // form
        $form = $crawler->selectButton('submit')->form();

        // Populate form
        $form['name'] = "";
        $form['dateCreated'] = "";
        $form['recipient'] = "";
        $form['status'] = "Complete";
        $form['dateFinished'] = "10/17/2017";
        $form['description'] = "Deliver stufff to school";

        // Submit the form to the crawler
        $crawler = $client->submit($form);

        // Test to see if our error message is on has appeared.
        $this->assertCount(3, $crawler->filter('.error'));
    }

    public function testDateCreatedFutureDate()
    {
        $client = static::createClient();

        $crawler = $client->request("GET",'/operation/edumat');

        // form
        $form = $crawler->selectButton('submit')->form();

        // Populate form (Assume current date is 10/16/2017)
        $form['dateCreated'] = "10/17/2017";

        // Submit the form to the crawler
        $crawler = $client->submit($form);

        // Test to see if our error message is on has appeared.
        $this->assertContains("You have entered a date in the future. You may only enter todays date, and dates in the past.", $client->getResponse()->getContent());
    }

    public function testDateFinishedFutureDate()
    {
        $client = static::createClient();

        $crawler = $client->request("GET",'/operation/edumat');

        // form
        $form = $crawler->selectButton('submit')->form();

        // Populate form (Assume current date is 10/16/2017)
        $form['dateFinished'] = "10/17/2017";

        // Submit the form to the crawler
        $crawler = $client->submit($form);

        // Test to see if our error message is on has appeared.
        $this->assertContains("You have entered a date in the future. You may only enter todays date, and dates in the past.", $client->getResponse()->getContent());
    }
}
