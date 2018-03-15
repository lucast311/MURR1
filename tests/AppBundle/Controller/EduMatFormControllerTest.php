<?php
namespace tests\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use AppBundle\Entity\EduMat;
use AppBundle\DataFixtures\ORM\LoadUserData;
use Tests\AppBundle\DatabasePrimer;

/**
 * story14a_csr_user_creates_new_educational_material - Tests
 */
class EduMatFormControllerTest extends WebTestCase
{
    private $em;

    public static function setUpBeforeClass()
    {
        self::bootKernel();
        DatabasePrimer::prime(self::$kernel);
    }


    /**
     * (@inheritDoc)
     */
    protected function setUp()
    {
        self::bootKernel();

        $this->em = static::$kernel->getContainer()
            ->get('doctrine')
            ->getManager();

        // Load the admin user into the database so they can log in
        $encoder = static::$kernel->getContainer()->get('security.password_encoder');

        $userLoader = new LoadUserData($encoder);
        $userLoader->load($this->em);
    }

    /**
     * test a successful submit
     */
    public function testSuccess()
    {
        // create a client
        $client = static::createClient(array(), array('PHP_AUTH_USER' => 'admin', 'PHP_AUTH_PW'   => 'password'));

        // create a crawler that will act like a user (enter data, then submit)
        $crawler = $client->request("GET",'EduMatForm');

        // select the button to press (doesn't exist yet, so the button is null)
        // This will throw an exception when the tests run, due to selecting a null
        $form = $crawler->selectButton('Add')->form();

        // Populate form
        $form['form[name]'] = "SchoolDelivery";
        $form['form[status]'] = 3;
        $form['form[dateCreated]'] = "2017-10-17";
        $form['form[dateFinished]'] = "2017-10-17";
        $form['form[description]'] = "Deliver stufff to school";
        $form['form[recipient]'] = "Hamburg School";

        // Submit the form to the crawler
        $crawler = $client->submit($form);

        // test that no errors were displayed to the page (submit was successful)
        $this->assertCount(0, $crawler->filter('li'));
    }

    /**
     * test that value entered into the name field was valid if the user enters
     *  a value that is equal to the max character length
     */
    public function testNameLengthEqualMax()
    {
        // create a client
        $client = static::createClient(array(), array('PHP_AUTH_USER' => 'admin', 'PHP_AUTH_PW'   => 'password'));

        // create a crawler that will act like a user (enter data, then submit)
        $crawler = $client->request("GET",'EduMatForm');

        // select the button to press
        $form = $crawler->selectButton('Add')->form();

        // Populate form
        $form['form[name]'] = "aaaaaaSchoolDeliveryaaaaaaaaa";
        $form['form[status]'] = 3;
        $form['form[dateCreated]'] = "2017-10-17";
        $form['form[dateFinished]'] = "2017-10-17";
        $form['form[description]'] = "Deliver stufff to school";
        $form['form[recipient]'] = "Hamburg School";

        $crawler = $client->submit($form);

        // test that no errors were displayed by the form
        $this->assertCount(0, $crawler->filter('li'));
    }

    /**
     * test that value entered into the name field was valid if the user enters
     *  a value that is equal to the max character length - 1
     */
    public function testNameLengthOneLessThenMax()
    {
        // create a client
        $client = static::createClient(array(), array('PHP_AUTH_USER' => 'admin', 'PHP_AUTH_PW'   => 'password'));

        // create a crawler that will act like a user (enter data, then submit)
        $crawler = $client->request("GET",'EduMatForm');

        // select the button to press
        $form = $crawler->selectButton('Add')->form();

        // Populate form
        $form['form[name]'] = "aaaaaaSchoolDeliveryaaaaaaaa";
        $form['form[status]'] = 3;
        $form['form[dateCreated]'] = "2017-10-17";
        $form['form[dateFinished]'] = "2017-10-17";
        $form['form[description]'] = "Deliver stufff to school";
        $form['form[recipient]'] = "Hamburg School";

        $crawler = $client->submit($form);

        // same tests as above, only with name length - 1
        $this->assertCount(0, $crawler->filter('li'));
    }

    /**
     * test that value entered into the name field was valid if the user enters
     *  a value that is equal to the minimum character length (length of 1)
     */
    public function testNameLengthOne()
    {
        // create a client
        $client = static::createClient(array(), array('PHP_AUTH_USER' => 'admin', 'PHP_AUTH_PW'   => 'password'));

        // create a crawler that will act like a user (enter data, then submit)
        $crawler = $client->request("GET",'EduMatForm');

        // select the button to press
        $form = $crawler->selectButton('Add')->form();

        // Populate form
        $form['form[name]'] = "s";
        $form['form[status]'] = 3;
        $form['form[dateCreated]'] = "2017-10-17";
        $form['form[dateFinished]'] = "2017-10-17";
        $form['form[description]'] = "Deliver stufff to school";
        $form['form[recipient]'] = "Hamburg School";

        $crawler = $client->submit($form);

        // same as above but with a single character
        $this->assertCount(0, $crawler->filter('li'));
    }

    /**
     * test that value entered into the name field was invalid if the user enters
     *  a value that contains anything other than letter characters
     */
    public function testNameCharacters()
    {
        // create a client
        $client = static::createClient(array(), array('PHP_AUTH_USER' => 'admin', 'PHP_AUTH_PW'   => 'password'));

        // create a crawler that will act like a user (enter data, then submit)
        $crawler = $client->request("GET",'EduMatForm');

        // select the button to press
        $form = $crawler->selectButton('Add')->form();

        // Populate form
        $form['form[name]'] = "School Delivery1";

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
        $client = static::createClient(array(), array('PHP_AUTH_USER' => 'admin', 'PHP_AUTH_PW'   => 'password'));

        // create a crawler that will act like a user (enter data, then submit)
        $crawler = $client->request("GET",'EduMatForm');

        // select the button to press
        $form = $crawler->selectButton('Add')->form();

        // Populate form
        $form['form[name]'] = "       ";

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
        $client = static::createClient(array(), array('PHP_AUTH_USER' => 'admin', 'PHP_AUTH_PW'   => 'password'));

        // create a crawler that will act like a user (enter data, then submit)
        $crawler = $client->request("GET",'EduMatForm');

        // select the button to press
        $form = $crawler->selectButton('Add')->form();

        // Populate form
        $form['form[name]'] = "s";
        $form['form[status]'] = 3;
        $form['form[dateCreated]'] = "2017-10-17";
        $form['form[dateFinished]'] = "2017-10-17";
        $form['form[description]'] = "sssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssss";
        $form['form[recipient]'] = "Hamburg School";

        $crawler = $client->submit($form);

        // same test as above only this uses description instead of name
        $this->assertCount(0, $crawler->filter('li'));
    }

    /**
     * test that value entered into the description field was valid if the user enters
     *  a value that is equal to the max character length - 1
     */
    public function testDescLengthOneLessThanMax()
    {
        // create a client
        $client = static::createClient(array(), array('PHP_AUTH_USER' => 'admin', 'PHP_AUTH_PW'   => 'password'));

        // create a crawler that will act like a user (enter data, then submit)
        $crawler = $client->request("GET",'EduMatForm');

        // select the button to press
        $form = $crawler->selectButton('Add')->form();

        // Populate form
        $form['form[name]'] = "s";
        $form['form[status]'] = 3;
        $form['form[dateCreated]'] = "2017-10-17";
        $form['form[dateFinished]'] = "2017-10-17";
        $form['form[description]'] = "ssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssss";
        $form['form[recipient]'] = "Hamburg School";

        $crawler = $client->submit($form);

        // same as above
        $this->assertCount(0, $crawler->filter('li'));
    }

    /**
     * test that value entered into the description field was valid if the user enters
     *  a value that is equal to a single character
     */
    public function testDescLengthOne()
    {
        // create a client
        $client = static::createClient(array(), array('PHP_AUTH_USER' => 'admin', 'PHP_AUTH_PW'   => 'password'));

        // create a crawler that will act like a user (enter data, then submit)
        $crawler = $client->request("GET",'EduMatForm');

        // select the button to press
        $form = $crawler->selectButton('Add')->form();

        // Populate form
        $form['form[name]'] = "s";
        $form['form[status]'] = 3;
        $form['form[dateCreated]'] = "2017-10-17";
        $form['form[dateFinished]'] = "2017-10-17";
        $form['form[description]'] = "s";
        $form['form[recipient]'] = "Hamburg School";

        $crawler = $client->submit($form);

        // same as above
        $this->assertCount(0, $crawler->filter('li'));
    }

    /**
     * test that value entered into the description field was valid if the user enters
     *  a value that is empty (this is not a required field)
     */
    public function testDescLengthZero()
    {
        // create a client
        $client = static::createClient(array(), array('PHP_AUTH_USER' => 'admin', 'PHP_AUTH_PW'   => 'password'));

        // create a crawler that will act like a user (enter data, then submit)
        $crawler = $client->request("GET",'EduMatForm');

        // select the button to press
        $form = $crawler->selectButton('Add')->form();

        // Populate form
        $form['form[name]'] = "s";
        $form['form[status]'] = 3;
        $form['form[dateCreated]'] = "2017-10-17";
        $form['form[dateFinished]'] = "2017-10-17";
        $form['form[description]'] = "";
        $form['form[recipient]'] = "Hamburg School";

        $crawler = $client->submit($form);

        // same as above
        $this->assertCount(0, $crawler->filter('li'));
    }

    /**
     * test that value entered into the recipient field was valid if the user enters
     *  a value that is equal to the max character length
     */
    public function testRecipientLengthEqualMax()
    {
        // create a client
        $client = static::createClient(array(), array('PHP_AUTH_USER' => 'admin', 'PHP_AUTH_PW'   => 'password'));

        // create a crawler that will act like a user (enter data, then submit)
        $crawler = $client->request("GET",'EduMatForm');

        // select the button to press
        $form = $crawler->selectButton('Add')->form();

        // Populate form
        $form['form[name]'] = "s";
        $form['form[status]'] = 3;
        $form['form[dateCreated]'] = "2017-10-17";
        $form['form[dateFinished]'] = "2017-10-17";
        $form['form[description]'] = "Deliver stufff to school";
        $form['form[recipient]'] = "sssssssssssssssssssssssssssssssssssssssssssssssss";

        $crawler = $client->submit($form);

        // same as above only this is for recipient
        $this->assertCount(0, $crawler->filter('li'));
    }

    /**
     * test that value entered into the recipient field was valid if the user enters
     *  a value that is equal to the max character length - 1
     */
    public function testRecipientLengthOneLessThanMax()
    {
        // create a client
        $client = static::createClient(array(), array('PHP_AUTH_USER' => 'admin', 'PHP_AUTH_PW'   => 'password'));

        // create a crawler that will act like a user (enter data, then submit)
        $crawler = $client->request("GET",'EduMatForm');

        // select the button to press
        $form = $crawler->selectButton('Add')->form();

        // Populate form
        $form['form[name]'] = "s";
        $form['form[status]'] = 3;
        $form['form[dateCreated]'] = "2017-10-17";
        $form['form[dateFinished]'] = "2017-10-17";
        $form['form[description]'] = "ssssssssssssssssssssssssssssssssssssssssssssssss";
        $form['form[recipient]'] = "Hamburg School";

        $crawler = $client->submit($form);

        // same as above
        $this->assertCount(0, $crawler->filter('li'));
    }

    /**
     * test that value entered into the recipient field was valid if the user enters
     *  a value that is equal to the minimum character length (length of 1)
     */
    public function testRecipientLengthOne()
    {
        // create a client
        $client = static::createClient(array(), array('PHP_AUTH_USER' => 'admin', 'PHP_AUTH_PW'   => 'password'));

        // create a crawler that will act like a user (enter data, then submit)
        $crawler = $client->request("GET",'EduMatForm');

        // select the button to press
        $form = $crawler->selectButton('Add')->form();

        // Populate form
        $form['form[name]'] = "s";
        $form['form[status]'] = 3;
        $form['form[dateCreated]'] = "2017-10-17";
        $form['form[dateFinished]'] = "2017-10-17";
        $form['form[description]'] = "s";
        $form['form[recipient]'] = "s";

        $crawler = $client->submit($form);

        // same as above
        $this->assertCount(0, $crawler->filter('li'));
    }

    /**
     * test that value entered into the recipient field was invalid if the user enters
     *  a value that contains any special caracters
     */
    public function testRecipientCharacters()
    {
        // create a client
        $client = static::createClient(array(), array('PHP_AUTH_USER' => 'admin', 'PHP_AUTH_PW'   => 'password'));

        // create a crawler that will act like a user (enter data, then submit)
        $crawler = $client->request("GET",'EduMatForm');

        // select the button to press
        $form = $crawler->selectButton('Add')->form();

        // Populate form
        $form['form[recipient]'] = "@Saskpolytech";

        // Submit the form to the crawler
        $crawler = $client->submit($form);

        // Test to see if our error message is on has appeared.
        $this->assertContains("You have entered invalid characters into this field. Please use only alphanumeric values, and spaces.", $client->getResponse()->getContent());
    }

    /**
     * test that value entered into the recipient field was invalid if the user enters
     *  a value that is just spaces (same as "name is spaces" test above)
     */
    public function testRecipientSpaces()
    {
        // create a client
        $client = static::createClient(array(), array('PHP_AUTH_USER' => 'admin', 'PHP_AUTH_PW'   => 'password'));

        // create a crawler that will act like a user (enter data, then submit)
        $crawler = $client->request("GET",'EduMatForm');

        // select the button to press
        $form = $crawler->selectButton('Add')->form();

        // Populate form
        $form['form[recipient]'] = "       ";

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
        $client = static::createClient(array(), array('PHP_AUTH_USER' => 'admin', 'PHP_AUTH_PW'   => 'password'));

        // create a crawler that will act like a user (enter data, then submit)
        $crawler = $client->request("GET",'EduMatForm');

        // select the button to press
        $form = $crawler->selectButton('Add')->form();

        // Populate form
        $form['form[name]'] = "";
        $form['form[dateCreated]'] = "";
        $form['form[recipient]'] = "";
        $form['form[status]'] = 3;
        $form['form[dateFinished]'] = "2017-10-17";
        $form['form[description]'] = "Deliver stufff to school";

        // Submit the form to the crawler
        $crawler = $client->submit($form);

        // Test to see if our error message is on has appeared.
        $this->assertCount(2, $crawler->filter('div.item'));
    }

    /**
     * (@inheritDoc)
     */
    protected function tearDown()
    {
        parent::tearDown();

        $stmt = $this->em->getConnection()->prepare("DELETE FROM User");
        $stmt->execute();

        $this->em->close();
        $this->em = null; //avoid memory meaks
    }



}