<?php

namespace Tests\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ContactControllerTest extends WebTestCase
{
    /**
     * Story 9a
     * Tests the list action. Ensures that a table exists in the html with the right headers.
     */
    public function testListAction()
    {
        //Create a client to go through the web page
        $client = static::createClient();
        //Request the contact add page
        $crawler = $client->request('GET','/contact/list');

        // Assert that a table exists
        $this->assertGreaterThan(0, $crawler->filter('table')->count());
        // Assert that the table has the proper headings
        $this->assertGreaterThan(0, $crawler->filter('html:contains("First Name")')->count());
        $this->assertGreaterThan(0, $crawler->filter('html:contains("Last Name")')->count());
        $this->assertGreaterThan(0, $crawler->filter('html:contains("Organization")')->count());
        $this->assertGreaterThan(0, $crawler->filter('html:contains("Primary Phone")')->count());
    }

    /**
     * Story 9a
     * Tests the viewing of a specific contact. Ensures that the page can be navigated to through the list
     * and that it contains all the required labels on the page.
     */
    public function testViewAction()
    {
        //Create a client to go through the web page
        $client = static::createClient();
        //Request the contact add page
        $crawler = $client->request('GET','/contact/list');
        // Select the first button on the page that views the details for a contact
        $link = $crawler->filter('a:contains("View")')->eq(0)->link();
        // Go there - should be viewing a specific contact after this
        $crawler = $client->click($link);

        // Assert that all the proper labels are on the page
        $this->assertGreaterThan(0, $crawler->filter('html:contains("First Name:")')->count());
        $this->assertGreaterThan(0, $crawler->filter('html:contains("Last Name:")')->count());
        $this->assertGreaterThan(0, $crawler->filter('html:contains("Organization:")')->count());
        $this->assertGreaterThan(0, $crawler->filter('html:contains("Primary Phone:")')->count());
        $this->assertGreaterThan(0, $crawler->filter('html:contains("Secondary Phone:")')->count());
        $this->assertGreaterThan(0, $crawler->filter('html:contains("Phone Extension:")')->count());
        $this->assertGreaterThan(0, $crawler->filter('html:contains("Email Address:")')->count());
        $this->assertGreaterThan(0, $crawler->filter('html:contains("Fax:")')->count());
        $this->assertGreaterThan(0, $crawler->filter('html:contains("Street Address:")')->count());
        $this->assertGreaterThan(0, $crawler->filter('html:contains("Postal Code:")')->count());
        $this->assertGreaterThan(0, $crawler->filter('html:contains("City:")')->count());
        $this->assertGreaterThan(0, $crawler->filter('html:contains("Province:")')->count());
        $this->assertGreaterThan(0, $crawler->filter('html:contains("Country:")')->count());
    }

    // story 9c tests
    public function testEditRedirect()
    {
        //Create a client to go through the web page
        $client = static::createClient();
        //Request the contact edit page
        $crawler = $client->request('GET','/contact/');
        // Select the first button on the page that views the details for a contact
        $link = $crawler->filter('a[href="/contact/1/edit"]')->eq(0)->link();
        // Go there - should be viewing a specific contact after this
        $crawler = $client->click($link);

        $this->assertGreaterThan(0, $crawler->filter($("h1:contains(Contact Edit)")->count());
    }

    public function testEditSubmitRedirect()
    {
        //Create a client to go through the web page
        $client = static::createClient();
        //Request the contact edit page
        $crawler = $client->request('GET','/contact/list/1/edit');
        // Select the first button on the page that views the details for a contact
        $link = $crawler->filter('a:contains("Edit")')->eq(0)->link();
        // Go there - should be viewing a specific contact after this
        $crawler = $client->click($link);

        $this->assertGreaterThan(0, $crawler->filter('html:contains("View contact")')->count());
    }
}

