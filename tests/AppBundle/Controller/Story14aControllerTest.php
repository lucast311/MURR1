<?php

namespace Tests\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class DefaultControllerTest extends WebTestCase
{
    public function testSuccess()
    {
        $client = static::createClient();
=
        $crawler = $client->request("GET",'/operation/edumat');

        // form
        $form = $crawler->selectButton('submit')->form();

        // Populate form
        $form['name'] = "SchoolDelivery";
        $form['status'] = "Complete";
        $form['dateCreated'] = "10/17/2017";
        $form['dateFinished'] = "10/17/2017";
        $form['Description'] = "Deliver stufff to school";
        $form['recipient'] = "Hamburg School";

        // Submit the form to the crawler
        $crawler = $client->submit($form);
    }

    public function testNameLength()
    {
        $client = static::createClient();
=
        $crawler = $client->request("GET",'/operation/edumat');

        // form
        $form = $crawler->selectButton('submit')->form();

        // Populate form
        $form['name'] = "aaaaaaSchoolDeliveryaaaaaaaaaaa";
        $form['status'] = "Complete";
        $form['dateCreated'] = "10/17/2017";
        $form['dateFinished'] = "10/17/2017";
        $form['Description'] = "Deliver stufff to school";
        $form['recipient'] = "Hamburg School";

        // Submit the form to the crawler
        $crawler = $client->submit($form);

        // Test to see if our error message is on has appeared.
        $this->assertContains("The maximum number of characters for this field is 30.",
                                $crawler->filter("#formError div")->text();
    }

    public function testNameLength()
    {
        $client = static::createClient();
=
        $crawler = $client->request("GET",'/operation/edumat');

        // form
        $form = $crawler->selectButton('submit')->form();

        // Populate form
        $form['name'] = "School Delivery1";
        $form['status'] = "Complete";
        $form['dateCreated'] = "10/17/2017";
        $form['dateFinished'] = "10/17/2017";
        $form['Description'] = "Deliver stufff to school";
        $form['recipient'] = "Hamburg School";

        // Submit the form to the crawler
        $crawler = $client->submit($form);

        // Test to see if our error message is on has appeared.
        $this->assertContains("The maximum number of characters for this field is 30.",
                                $crawler->filter("#formError div")->text();
    }
}
