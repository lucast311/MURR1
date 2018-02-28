<?php
namespace Tests\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use AppBundle\Entity\User;
use AppBundle\DataFixtures\ORM\LoadUserData;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Tests\AppBundle\DatabasePrimer;

class SecurityControllerTest extends WebTestCase
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

        $encoder = static::$kernel->getContainer()->get('security.password_encoder');

        $userLoader = new LoadUserData($encoder);
        $userLoader->load($this->em);
    }

    /**
     * Story 15a
     * Tests that the route works and that the fields are on the page
     */
    public function testLoginPageExists()
    {
        // Create client
        $client = static::createClient();
        // Go to the login page
        $crawler = $client->request('GET', '/login');
        // Get the response
        $response = $client->getResponse()->getContent();

        // Make sure there is the correct items on the screen
        $this->assertcontains("Log In", $response);
        $this->assertcontains("User Name", $response);
        $this->assertcontains("Password", $response);
    }

    /**
     * story 15a
     * Tests that the user can log in successfully and that they are properly redirected to the main page
     */
    public function testSuccessfulLogin()
    {
        // Create client
        $client = static::createClient();
        // Go to the login page
        $crawler = $client->request('GET', '/login');
        // Select the login form
        $form = $crawler->selectButton('Log In')->form();

        // Fill out the login form
        // Might need appbundle_
        $form['_username'] = "admin";
        $form['_password'] = 'password';
        // Submit the login form
        $crawler = $client->submit($form);

        // Assert that we were redirected to the main page
        $this->assertRegExp('/\//', $client->getResponse()->headers->get('location'));
    }

    /**
     * story 15a
     * Tests that when a not logged in user navigates to a protected page they get redirected to log in. Then check that they are redirected to the page they requested.
     */
    public function testNotLoggedInRedirect()
    {
        // Create client
        $client = static::createClient();
        $client->followRedirects(true);

        // Go to a bad page
        $crawler = $client->request('GET', '/contact/new');

        // Assert that the user was actually redirected
        $this->assertContains("/login", $client->getHistory()->current()->getUri());

        // Select the login form
        $form = $crawler->selectButton('Log In')->form();

        // Fill out the login form
        // Might need appbundle_
        $form['_username'] = "admin";
        $form['_password'] = 'password';
        // Submit the login form
        $crawler = $client->submit($form);

        // Assert that we were redirected to the initially requested page
        $this->assertContains("/contact/new", $client->getHistory()->current()->getUri());
    }

    /**
     * story 15a
     * Test that if a user is already logged in and they browse to the login page that they are redirected to the main page.
     */
    public function testLoggedInGoToLoginPageRedirect()
    {
        // Create client
        $client = static::createClient();
        // Go to the login page
        $crawler = $client->request('GET', '/login');
        // Select the login form
        $form = $crawler->selectButton('Log In')->form();

        // Fill out the login form
        // Might need appbundle_
        $form['_username'] = "admin";
        $form['_password'] = 'password';
        // Submit the login form
        $crawler = $client->submit($form);

        // Go back to the login page
        $crawler = $client->request('GET', '/login');

        // Assert that we were dumped back at the main page
        $this->assertTrue($client->getResponse() instanceof RedirectResponse);
        $this->assertTrue($client->getResponse()->isRedirect('/'));
    }

    /**
     * story 15a
     * Check a failed log in that a message appears
     */
    public function testFailureLoggingIn()
    {
        // Create client
        $client = static::createClient();
        $client->followRedirects(true);
        // Go to the login page
        $crawler = $client->request('GET', '/login');
        // Select the login form
        $form = $crawler->selectButton('Log In')->form();

        // Fill out the login form
        // Might need appbundle_
        $form['_username'] = "admin3";
        $form['_password'] = 'asdfdf';
        // Submit the login form
        $crawler = $client->submit($form);

        // Assert that we are still at the login page
        $this->assertContains("/login", $client->getHistory()->current()->getUri());

        // Assert that the error message is on the screen
        $this->assertcontains("Invalid credentials.", $client->getResponse()->getContent());
    }

    /**
     * story 15a
     * check that a logged in user cannot go to a page that they are not permitted to access.
     */
    public function testGoToForbiddenPage()
    {
        // Create client
        $client = static::createClient();
        $client->followRedirects(true);
        // Go to the login page
        $crawler = $client->request('GET', '/login');
        // Select the login form
        $form = $crawler->selectButton('Log In')->form();

        // Fill out the login form
        // Might need appbundle_
        $form['_username'] = "admin";
        $form['_password'] = 'password';
        // Submit the login form
        $crawler = $client->submit($form);

        // Now that we are logged in, try to go to the forbidden page
        $crawler = $client->request('GET', '/forbidden');
        // Assert that there is an error on the page
        // this is the symfony dev profile error page
        $this->assertcontains("HTTP 403", $client->getResponse()->getContent());

        //make sure that our custom error can be viewed
        $crawler = $client->request('GET', '/_error/403');
        $this->assertcontains("You do not have permission to access this resource.", $client->getResponse()->getContent());
    }

    /**
     * (@inheritDoc)
     */
    protected function tearDown()
    {
        parent::tearDown();

        $stmt = $this->em->getConnection()->prepare("DELETE FROM User");
        $stmt->execute();
        $stmt = $this->em->getConnection()->prepare('DELETE FROM Address');
        $stmt->execute();

        $this->em->close();
        $this->em = null; //avoid memory meaks
    }
}