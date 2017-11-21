<?php
namespace Tests\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ContactControllerTest extends WebTestCase
{

    /**
     * story 9f
     * A test to ensure an entry isn't submitted blank.
     */
    public function testAddActionFailureEmptyForm()
    {
        // Create a client to go through the web form
        $client = static::createClient;
        // Create a crawler to request the page
        $crawler = $client->request('GET','/contact/add');
        // Select the form and add values to it
        $form = $crawler->selectButton('Save')->form;
        $form['contact[firstName]'] = '';
        $form['contact[lastName]'] = '';
        $form['contact[role]'] = '';
        $form['contact[primaryPhone]'] = '';
        $form['contact[extension]'] = '';
        $form['contact[primaryPhone]'] = '';
        $form['contact[secondaryPhone]'] = '';
        $form['contact[emailAddress]'] = '';
        $form['contact[fax]'] = '';
        $form['contact[property]'] = '';
        $form['contact[address][streetAddress]'] = '';
        $form['contact[address][postalCode]'] = '';
        $form['contact[address][city]'] = '';
        $form['contact[address][province]'] = '';
        $form['contact[address][country]'] = '';

        // submit the form
        $crawler = $client->submit($form);
        // Check to see if the failure message has appeared
        $this->assertCount(1,
            $crawler->filter(
                'html:contains("Please fill out the form.")')->count()
            );
    }
    /**
     * story 9f
     * A test to ensure the contact entry has at least one piece of personal info. such as
     * a first name or last name.
     */
    public function testAddActionFailureNoPersonalInfo()
    {
        // Create a client to go through the web form
        $client = static::createClient;
        // Create a crawler to request the page
        $crawler = $client->request('GET','/contact/add');
        // Select the form and add values to it
        $form = $crawler->selectButton('Save')->form;
        $form['contact[firstName]'] = '';
        $form['contact[lastName]'] = '';
        $form['contact[role]'] = '';
        $form['contact[primaryPhone]'] = '306-854-2486';
        $form['contact[extension]'] = '';
        $form['contact[primaryPhone]'] = '';
        $form['contact[secondaryPhone]'] = '';
        $form['contact[emailAddress]'] = '';
        $form['contact[fax]'] = '';
        $form['contact[property]'] = '';
        $form['contact[address][streetAddress]'] = '123 Main Street';
        $form['contact[address][postalCode]'] = 'S7N 0R7';
        $form['contact[address][city]'] = 'Saskatoon';
        $form['contact[address][province]'] = 'Saskatchewan';
        $form['contact[address][country]'] = 'Canada';

        // submit the form
        $crawler = $client->submit($form);
        // Check to see if the failure message has appeared
        $this->assertCount(1,
            $crawler->filter(
                'html:contains("Contacts must have at least one piece of personal information. (First name, last name, or email)")')->count()
            );
    }
    /**
     * story9f
     * A test to ensure the contact entry has at least one piece of contact info,
     * such as an email, phone number, or fax.
     */
    public function testAddActionFailureNoContactInfo()
    {
        // Create a client to go through the web form
        $client = static::createClient;
        // Create a crawler to request the page
        $crawler = $client->request('GET','/contact/add');
        // Select the form and add values to it
        $form = $crawler->selectButton('Save')->form;
        $form['contact[firstName]'] = 'Luke';
        $form['contact[lastName]'] = 'Skywalker';
        $form['contact[role]'] = '';
        $form['contact[primaryPhone]'] = '';
        $form['contact[extension]'] = '';
        $form['contact[primaryPhone]'] = '';
        $form['contact[secondaryPhone]'] = '';
        $form['contact[emailAddress]'] = '';
        $form['contact[fax]'] = '';
        $form['contact[property]'] = '';
        $form['contact[address][streetAddress]'] = '123 Main Street';
        $form['contact[address][postalCode]'] = 'S7N 0R7';
        $form['contact[address][city]'] = 'Saskatoon';
        $form['contact[address][province]'] = 'Saskatchewan';
        $form['contact[address][country]'] = 'Canada';

        // submit the form
        $crawler = $client->submit($form);
        // Check to see if the failure message has appeared
        $this->assertCount(1,
            $crawler->filter(
                'html:contains("Contacts must have at least one piece of contact information. (Phone number, email, fax)")')->count()
            );
    }
    /**
     * story 9f
     * A test to make sure the role field has less than 101 characters
     */
    public function testRoleCharacterLimit()
    {
        // Create a client to go through the web form
        $client = static::createClient;
        // Create a crawler to request the page
        $crawler = $client->request('GET','/contact/add');
        // Select the form and add values to it
        $form = $crawler->selectButton('Save')->form;
        $form['contact[firstName]'] = 'Jeremy';
        $form['contact[lastName]'] = 'Dunkan';
        $form['contact[role]'] = 'saaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa
                                  aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaae';
        $form['contact[primaryPhone]'] = '306-854-2486';
        $form['contact[extension]'] = '';
        $form['contact[primaryPhone]'] = '';
        $form['contact[secondaryPhone]'] = '';
        $form['contact[emailAddress]'] = '';
        $form['contact[fax]'] = '';
        $form['contact[property]'] = '';
        $form['contact[address][streetAddress]'] = '123 Main Street';
        $form['contact[address][postalCode]'] = 'S7N 0R7';
        $form['contact[address][city]'] = 'Saskatoon';
        $form['contact[address][province]'] = 'Saskatchewan';
        $form['contact[address][country]'] = 'Canada';

        // submit the form
        $crawler = $client->submit($form);
        // Check to see if the failure message has appeared
        $this->assertCount(1,
            $crawler->filter(
                'html:contains("Please keep the Role field under 100 characters.")')->count()
            );
    }
    /**
     * story 9f
     * A test to make sure the property field has less than 151 characters
     */
    public function testPropertyCharacterLimit()
    {
        // Create a client to go through the web form
        $client = static::createClient;
        // Create a crawler to request the page
        $crawler = $client->request('GET','/contact/add');

        $form = $crawler->selectButton('Save')->form;
        $form['contact[firstName]'] = 'Jeremy';
        $form['contact[lastName]'] = 'Dunkan';
        $form['contact[role]'] = 'Inspection Agent';
        $form['contact[primaryPhone]'] = '306-854-2486';
        $form['contact[extension]'] = '';
        $form['contact[primaryPhone]'] = '';
        $form['contact[secondaryPhone]'] = '';
        $form['contact[emailAddress]'] = '';
        $form['contact[fax]'] = '';
        $form['contact[property]'] = 'saaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa
                                      aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaae';
        $form['contact[address][streetAddress]'] = '123 Main Street';
        $form['contact[address][postalCode]'] = 'S7N 0R7';
        $form['contact[address][city]'] = 'Saskatoon';
        $form['contact[address][province]'] = 'Saskatchewan';
        $form['contact[address][country]'] = 'Canada';

        // submit the form
        $crawler = $client->submit($form);
        // Check to see if the failure message has appeared
        $this->assertCount(1,
            $crawler->filter(
                'html:contains("Please keep the Property field under 150 characters.")')->count()
            );
    }

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