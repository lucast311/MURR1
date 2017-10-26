<?php
namespace tests\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use AppBundle\Entity\EduMat;

/**
 * story14a_csr_user_creates_new_educational_material - Tests
 */
class DefaultControllerTest extends WebTestCase
{
    /**
     * test a successful submit
     */
    public function testSuccess()
    {
        // create a client
        $client = static::createClient();

        // create a crawler that will act like a user (enter data, then submit)
        $crawler = $client->request("GET",'/operation/edumat');

        // select the button to press (doesn't exist yet, so the button is null)
        // This will throw an exception when the tests run, due to selecting a null
        $form = $crawler->selectButton('submit')->form();

        // Populate form
        $form['name'] = "SchoolDelivery";
        $form['status'] = "Complete";
        $form['dateCreated'] = "2017-10-17";
        $form['dateFinished'] = "2017-10-17";
        $form['description'] = "Deliver stufff to school";
        $form['recipient'] = "Hamburg School";

        // Submit the form to the crawler
        $crawler = $client->submit($form);

        // test that no errors were displayed to the page (submit was successful)
        $this->assertCount(0, $crawler->filter('.error'));
    }

    /**
     * test that value entered into the name field was valid if the user enters
     *  a value that is equal to the max character length
     */
    public function testNameLengthEqualMax()
    {
        // create a client
        $client = static::createClient();

        // create a crawler that will act like a user (enter data, then submit)
        $crawler = $client->request("GET",'/operation/edumat');

        // select the button to press
        $form = $crawler->selectButton('submit')->form();

        // Populate form
        $form['name'] = "aaaaaaSchoolDeliveryaaaaaaaaa";
        $form['status'] = "Complete";
        $form['dateCreated'] = "2017-10-17";
        $form['dateFinished'] = "2017-10-17";
        $form['description'] = "Deliver stufff to school";
        $form['recipient'] = "Hamburg School";

        // create a new EduMat with the name
        $edu = new EduMat($form['name']);

        // test that the name of the EduMat matches the value submitted by the form
        $this->assertTrue($edu->getName() === "aaaaaaSchoolDeliveryaaaaaaaaa");

        // test that no errors were displayed by the form
        $this->assertCount(0, $crawler->filter('.error'));
    }

    /**
     * test that value entered into the name field was valid if the user enters
     *  a value that is equal to the max character length - 1
     */
    public function testNameLengthOneLessThenMax()
    {
        // create a client
        $client = static::createClient();

        // create a crawler that will act like a user (enter data, then submit)
        $crawler = $client->request("GET",'/operation/edumat');

        // select the button to press
        $form = $crawler->selectButton('submit')->form();

        // Populate form
        $form['name'] = "aaaaaaSchoolDeliveryaaaaaaaa";
        $form['status'] = "Complete";
        $form['dateCreated'] = "2017-10-17";
        $form['dateFinished'] = "2017-10-17";
        $form['description'] = "Deliver stufff to school";
        $form['recipient'] = "Hamburg School";

        // same tests as above, only with name length - 1
        $edu = new EduMat($form['name']);
        $this->assertTrue($edu->getName() === "aaaaaaSchoolDeliveryaaaaaaaa");
        $this->assertCount(0, $crawler->filter('.error'));
    }

    /**
     * test that value entered into the name field was valid if the user enters
     *  a value that is equal to the minimum character length (length of 1)
     */
    public function testNameLengthOne()
    {
        // create a client
        $client = static::createClient();

        // create a crawler that will act like a user (enter data, then submit)
        $crawler = $client->request("GET",'/operation/edumat');

        // select the button to press
        $form = $crawler->selectButton('submit')->form();

        // Populate form
        $form['name'] = "s";
        $form['status'] = "Complete";
        $form['dateCreated'] = "2017-10-17";
        $form['dateFinished'] = "2017-10-17";
        $form['description'] = "Deliver stufff to school";
        $form['recipient'] = "Hamburg School";

        // same as above but with a single character
        $edu = new EduMat($form['name']);
        $this->assertTrue($edu->getName() === "s");
        $this->assertCount(0, $crawler->filter('.error'));
    }

    /**
     * test that value entered into the name field was invalid if the user enters
     *  a value that contains anything other than letter characters
     */
    public function testNameCharacters()
    {
        // create a client
        $client = static::createClient();

        // create a crawler that will act like a user (enter data, then submit)
        $crawler = $client->request("GET",'/operation/edumat');

        // select the button to press
        $form = $crawler->selectButton('submit')->form();

        // Populate form
        $form['name'] = "School Delivery1";

        // Submit the form to the crawler
        $crawler = $client->submit($form);

        // Test to see if our error message is on has appeared.
        $this->assertContains("You have entered invalid characters into the name field. Please use letter names only. Additional characters may be used in the description field.",
                                $client->getResponse()->getContent());
    }

    /**
     * test that value entered into the name field was valid if the user enters
     *  a value that is made up of nothing but spaces (we will trim this to see if
     *  the value comes back as an empty string)
     */
    public function testNameSpaces()
    {
        // create a client
        $client = static::createClient();

        // create a crawler that will act like a user (enter data, then submit)
        $crawler = $client->request("GET",'/operation/edumat');

        // select the button to press
        $form = $crawler->selectButton('submit')->form();

        // Populate form
        $form['name'] = "       ";

        // Submit the form to the crawler
        $crawler = $client->submit($form);

        // Test to see if our error message is on has appeared.
        $this->assertContains("This field is required.", $client->getResponse()->getContent());
    }

    /**
     * test that value entered into the description field was valid if the user enters
     *  a value that is equal to the max character length
     */
    public function testDescLengthEqualMax()
    {
        // create a client
        $client = static::createClient();

        // create a crawler that will act like a user (enter data, then submit)
        $crawler = $client->request("GET",'/operation/edumat');

        // select the button to press
        $form = $crawler->selectButton('submit')->form();

        // Populate form
        $form['name'] = "s";
        $form['status'] = "Complete";
        $form['dateCreated'] = "2017-10-17";
        $form['dateFinished'] = "2017-10-17";
        $form['description'] = "sssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssss";
        $form['recipient'] = "Hamburg School";

        // same test as above only this uses description instead of name
        $edu = new EduMat($form['name'], $form['status'], $form['dateCreated'], $form['dateFinished'], $form['recipient'], $form['description']);
        $this->assertTrue($edu->getDescription() === "sssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssss");
        $this->assertCount(0, $crawler->filter('.error'));
    }

    /**
     * test that value entered into the description field was valid if the user enters
     *  a value that is equal to the max character length - 1
     */
    public function testDescLengthOneLessThanMax()
    {
        // create a client
        $client = static::createClient();

        // create a crawler that will act like a user (enter data, then submit)
        $crawler = $client->request("GET",'/operation/edumat');

        // select the button to press
        $form = $crawler->selectButton('submit')->form();

        // Populate form
        $form['name'] = "s";
        $form['status'] = "Complete";
        $form['dateCreated'] = "2017-10-17";
        $form['dateFinished'] = "2017-10-17";
        $form['description'] = "ssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssss";
        $form['recipient'] = "Hamburg School";

        // same as above
        $edu = new EduMat($form['name'], $form['status'], $form['dateCreated'], $form['dateFinished'], $form['recipient'], $form['description']);
        $this->assertTrue($edu->getDescription() === "ssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssss");
        $this->assertCount(0, $crawler->filter('.error'));
    }

    /**
     * test that value entered into the description field was valid if the user enters
     *  a value that is equal to a single character
     */
    public function testDescLengthOne()
    {
        // create a client
        $client = static::createClient();

        // create a crawler that will act like a user (enter data, then submit)
        $crawler = $client->request("GET",'/operation/edumat');

        // select the button to press
        $form = $crawler->selectButton('submit')->form();

        // Populate form
        $form['name'] = "s";
        $form['status'] = "Complete";
        $form['dateCreated'] = "2017-10-17";
        $form['dateFinished'] = "2017-10-17";
        $form['description'] = "s";
        $form['recipient'] = "Hamburg School";

        // same as above
        $edu = new EduMat($form['name'], $form['status'], $form['dateCreated'], $form['dateFinished'], $form['recipient'], $form['description']);
        $this->assertTrue($edu->getDescription() === "s");
        $this->assertCount(0, $crawler->filter('.error'));
    }

    /**
     * test that value entered into the description field was valid if the user enters
     *  a value that is empty (this is not a required field)
     */
    public function testDescLengthZero()
    {
        // create a client
        $client = static::createClient();

        // create a crawler that will act like a user (enter data, then submit)
        $crawler = $client->request("GET",'/operation/edumat');

        // select the button to press
        $form = $crawler->selectButton('submit')->form();

        // Populate form
        $form['name'] = "s";
        $form['status'] = "Complete";
        $form['dateCreated'] = "2017-10-17";
        $form['dateFinished'] = "2017-10-17";
        $form['description'] = "";
        $form['recipient'] = "Hamburg School";

        // same as above
        $edu = new EduMat($form['name'], $form['status'], $form['dateCreated'], $form['dateFinished'], $form['recipient'], $form['description']);
        $this->assertTrue($edu->getDescription() === "");
        $this->assertCount(0, $crawler->filter('.error'));
    }

    /**
     * test that value entered into the recipient field was valid if the user enters
     *  a value that is equal to the max character length
     */
    public function testRecipientLengthEqualMax()
    {
        // create a client
        $client = static::createClient();

        // create a crawler that will act like a user (enter data, then submit)
        $crawler = $client->request("GET",'/operation/edumat');

        // select the button to press
        $form = $crawler->selectButton('submit')->form();

        // Populate form
        $form['name'] = "s";
        $form['status'] = "Complete";
        $form['dateCreated'] = "2017-10-17";
        $form['dateFinished'] = "2017-10-17";
        $form['description'] = "Deliver stufff to school";
        $form['recipient'] = "sssssssssssssssssssssssssssssssssssssssssssssssss";

        // same as above only this is for recipient
        $edu = new EduMat($form['name'], $form['status'], $form['dateCreated'], $form['dateFinished'], $form['recipient'], $form['description']);
        $this->assertTrue($edu->getRecipient() === "sssssssssssssssssssssssssssssssssssssssssssssssss");
        $this->assertCount(0, $crawler->filter('.error'));
    }

    /**
     * test that value entered into the recipient field was valid if the user enters
     *  a value that is equal to the max character length - 1
     */
    public function testRecipientLengthOneLessThanMax()
    {
        // create a client
        $client = static::createClient();

        // create a crawler that will act like a user (enter data, then submit)
        $crawler = $client->request("GET",'/operation/edumat');

        // select the button to press
        $form = $crawler->selectButton('submit')->form();

        // Populate form
        $form['name'] = "s";
        $form['status'] = "Complete";
        $form['dateCreated'] = "2017-10-17";
        $form['dateFinished'] = "2017-10-17";
        $form['description'] = "ssssssssssssssssssssssssssssssssssssssssssssssss";
        $form['recipient'] = "Hamburg School";

        // same as above
        $edu = new EduMat($form['name'], $form['status'], $form['dateCreated'], $form['dateFinished'], $form['recipient'], $form['description']);
        $this->assertTrue($edu->getDescription() === "ssssssssssssssssssssssssssssssssssssssssssssssss");
        $this->assertCount(0, $crawler->filter('.error'));
    }

    /**
     * test that value entered into the recipient field was valid if the user enters
     *  a value that is equal to the minimum character length (length of 1)
     */
    public function testRecipientLengthOne()
    {
        // create a client
        $client = static::createClient();

        // create a crawler that will act like a user (enter data, then submit)
        $crawler = $client->request("GET",'/operation/edumat');

        // select the button to press
        $form = $crawler->selectButton('submit')->form();

        // Populate form
        $form['name'] = "s";
        $form['status'] = "Complete";
        $form['dateCreated'] = "2017-10-17";
        $form['dateFinished'] = "2017-10-17";
        $form['description'] = "s";
        $form['recipient'] = "s";

        // same as above
        $edu = new EduMat($form['name'], $form['status'], $form['dateCreated'], $form['dateFinished'], $form['recipient'], $form['description']);
        $this->assertTrue($edu->getRecipient() === "s");
        $this->assertCount(0, $crawler->filter('.error'));
    }

    /**
     * test that value entered into the recipient field was invalid if the user enters
     *  a value that contains any special caracters
     */
    public function testRecipientCharacters()
    {
        // create a client
        $client = static::createClient();

        // create a crawler that will act like a user (enter data, then submit)
        $crawler = $client->request("GET",'/operation/edumat');

        // select the button to press
        $form = $crawler->selectButton('submit')->form();

        // Populate form
        $form['recipient'] = "@Saskpolytech";

        // Submit the form to the crawler
        $crawler = $client->submit($form);

        // Test to see if our error message is on has appeared.
        $this->assertContains("You have entered invalid characters into this field. Please use Alpha-numeric (letters/numbers) characters only.", $client->getResponse()->getContent());
    }

    /**
     * test that value entered into the recipient field was invalid if the user enters
     *  a value that is just spaces (same as "name is spaces" test above)
     */
    public function testRecipientSpaces()
    {
        // create a client
        $client = static::createClient();

        // create a crawler that will act like a user (enter data, then submit)
        $crawler = $client->request("GET",'/operation/edumat');

        // select the button to press
        $form = $crawler->selectButton('submit')->form();

        // Populate form
        $form['recipient'] = "       ";

        // Submit the form to the crawler
        $crawler = $client->submit($form);

        // Test to see if our error message is on has appeared.
        $this->assertContains("This field is required.", $client->getResponse()->getContent());
    }

    /**
     * Test that all required fields display errors if they are submitted without values
     */
    public function testRequired()
    {
        // create a client
        $client = static::createClient();

        // create a crawler that will act like a user (enter data, then submit)
        $crawler = $client->request("GET",'/operation/edumat');

        // select the button to press
        $form = $crawler->selectButton('submit')->form();

        // Populate form
        $form['name'] = "";
        $form['dateCreated'] = "";
        $form['recipient'] = "";
        $form['status'] = "Complete";
        $form['dateFinished'] = "2017-10-17";
        $form['description'] = "Deliver stufff to school";

        // Submit the form to the crawler
        $crawler = $client->submit($form);

        // Test to see if our error message is on has appeared.
        $this->assertCount(3, $crawler->filter('.error'));
    }

    /**
     * test that value entered into the dateCreated field was invalid if the user enters
     *  a value that is set in the future
     */
    public function testDateCreatedFutureDate()
    {
        // create a client
        $client = static::createClient();

        // create a crawler that will act like a user (enter data, then submit)
        $crawler = $client->request("GET",'/operation/edumat');

        // select the button to press
        $form = $crawler->selectButton('submit')->form();

        // Populate form (Assume current date is 10/16/2017)
        $form['dateCreated'] = "2017-10-17";

        // Submit the form to the crawler
        $crawler = $client->submit($form);

        // Test to see if our error message is on has appeared.
        $this->assertContains("You have entered a date in the future. You may only enter todays date, and dates in the past.", $client->getResponse()->getContent());
    }

    /**
     * test that value entered into the dateFinished field was invalid if the user enters
     *  a value that is set in the future
     */
    public function testDateFinishedFutureDate()
    {
        // create a client
        $client = static::createClient();

        // create a crawler that will act like a user (enter data, then submit)
        $crawler = $client->request("GET",'/operation/edumat');

        // select the button to press
        $form = $crawler->selectButton('submit')->form();

        // Populate form (Assume current date is 10/16/2017)
        $form['dateFinished'] = "2017-10-17";

        // Submit the form to the crawler
        $crawler = $client->submit($form);

        // Test to see if our error message is on has appeared.
        $this->assertContains("You have entered a date in the future. You may only enter todays date, and dates in the past.", $client->getResponse()->getContent());
    }


    // Unused methods - We determined that the system will do these automatically, and will not need to be tested


    ////public function testNameLength()
    ////{
    ////    $client = static::createClient();

    ////    // create a crawler that will act like a user (enter data, then submit)
    ////    $crawler = $client->request("GET",'/operation/edumat');

    ////    // select the button to press
    ////    $form = $crawler->selectButton('submit')->form();

    ////    // Populate form
    ////    $form['name'] = "aaaaaaSchoolDeliveryaaaaaaaaaaa";

    ////    // Submit the form to the crawler
    ////    $crawler = $client->submit($form);

    ////    // Test to see if our error message is on has appeared.
    ////    $this->assertContains("The maximum number of characters for this field is 30.", $client->getResponse()->getContent());
    ////}

    ////public function testDescLength()
    ////{
    ////    $client = static::createClient();

    ////    $crawler = $client->request("GET",'/operation/edumat');

    ////    // form
    ////    $form = $crawler->selectButton('submit')->form();

    ////    // Populate form
    ////    $form['description'] = "sssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssss";

    ////    // Submit the form to the crawler
    ////    $crawler = $client->submit($form);

    ////    // Test to see if our error message is on has appeared.
    ////    $this->assertContains("The description you have entered is 430 characters long. The maximum number of characters for this field is 250.",
    ////                            $client->getResponse()->getContent());
    ////}

    ////public function testRecipientLength()
    ////{
    ////    $client = static::createClient();

    ////    $crawler = $client->request("GET",'/operation/edumat');

    ////    // form
    ////    $form = $crawler->selectButton('submit')->form();

    ////    // Populate form
    ////    $form['recipient'] = "sssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssss";

    ////    // Submit the form to the crawler
    ////    $crawler = $client->submit($form);

    ////    // Test to see if our error message is on has appeared.
    ////    $this->assertContains("The recipient you have entered is 114 characters long. The maximum number of characters for this field is 50.",
    ////                            $client->getResponse()->getContent());
    ////}

    //public function testDateCreatedFormatValid()
    //{
    //    $client = static::createClient();

    //    // create a crawler that will act like a user (enter data, then submit)
    //    $crawler = $client->request("GET",'/operation/edumat');

    //    // select the button to press
    //    $form = $crawler->selectButton('submit')->form();

    //    // Populate form
    //    $form['name'] = "s";
    //    $form['status'] = "Complete";
    //    $form['dateCreated'] = "2017-10-17";
    //    $form['dateFinished'] = "10/17/2017";
    //    $form['description'] = "s";
    //    $form['recipient'] = "s";

    //    $edu = new EduMat($form['name'], $form['status'], $form['dateCreated'], $form['dateFinished'], $form['recipient'], $form['description']);

    //    // dateMatch() method will return true or false depending on whether or not the entered date matches a specified format
    //    $this->assertTrue(dateMatch($edu->getDateCreated()));
    //    $this->assertCount(0, $crawler->filter('.error'));
    //}

    //public function testDateCreatedFormatInvalid()
    //{
    //    $client = static::createClient();

    //    // create a crawler that will act like a user (enter data, then submit)
    //    $crawler = $client->request("GET",'/operation/edumat');

    //    // select the button to press
    //    $form = $crawler->selectButton('submit')->form();

    //    // Populate form
    //    $form['name'] = "s";
    //    $form['status'] = "Complete";
    //    $form['dateCreated'] = "10/17/2017";
    //    $form['dateFinished'] = "2017-10-17";
    //    $form['description'] = "s";
    //    $form['recipient'] = "s";

    //    $edu = new EduMat($form['name'], $form['status'], $form['dateCreated'], $form['dateFinished'], $form['recipient'], $form['description']);

    //    // dateMatch() method will return true or false depending on whether or not the entered date matches a specified format
    //    $this->assertFalse(dateMatch($edu->getDateCreated()));
    //    $this->assertCount(0, $crawler->filter('.error'));
    //}
}