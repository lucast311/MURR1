<?php
namespace tests\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use AppBundle\DataFixtures\ORM\LoadUserData;
use Tests\AppBundle\DatabasePrimer;

/**
 * CSROOPsControllerTest short summary.
 *
 * CSROOPsControllerTest description.
 *
 * @version 1.0
 * @author cst201
 */
class CSROOPsControllerTest extends WebTestCase
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

    public function testNewOOPsActionSuccess()
    {

        //Create a client to go through the web page
        $client = static::createClient(array(), array('PHP_AUTH_USER' => 'admin', 'PHP_AUTH_PW'   => 'password'));
        $client->followRedirects(true);

        //Reques the contact add page
        $crawler = $client->request('POST','/oops/add');

        $form = $crawler->selectButton('Create OOPs Notice')->form();
        $form['form[binSerial]'] = 'testOOPs66';
        $form['form[problemType]'] = 'Contamination';
        $form['form[description]'] = 'Filled with Garbage';
        //$form['form[imageFile]'] = 'N;';

        $crawler = $client->submit($form);
        $this->assertNotContains("Create OOPs Notice",$client->getResponse()->getContent());
        //$this->assertContains("Communication added successfully",$client->getResponse()->getContent());
    }

    public function testNewOOPsActionFailureNoSerial()
    {

        //Create a client to go through the web page
        $client = static::createClient(array(), array('PHP_AUTH_USER' => 'admin', 'PHP_AUTH_PW'   => 'password'));
        $client->followRedirects(true);

        //Reques the contact add page
        $crawler = $client->request('POST','/oops/add');

        $form = $crawler->selectButton('Create OOPs Notice')->form();
        $form['form[binSerial]'] = '';
        $form['form[problemType]'] = 'Contamination';
        $form['form[description]'] = 'Filled with Garbage';
        //$form['form[imageFile]'] = 'N;';

        $crawler = $client->submit($form);
        $this->assertGreaterThan(
            0,
            $crawler->filter('html:contains("Create OOPs Notice")')->count()
            );
    }

    public function testNewActionFailureBinSerialSmall()
    {

        //Create a client to go through the web page
        $client = static::createClient(array(), array('PHP_AUTH_USER' => 'admin', 'PHP_AUTH_PW'   => 'password'));
        $client->followRedirects(true);

        //Reques the contact add page
        $crawler = $client->request('POST','/oops/add');

        $form = $crawler->selectButton('Create OOPs Notice')->form();
        $form['form[binSerial]'] = 'testOOPs6';
        $form['form[problemType]'] = 'Damage';
        $form['form[description]'] = 'test oops description';
        $form['form[imageFile]'] = 'N;';

        $crawler = $client->submit($form);
        $this->assertGreaterThan(
        0,
        $crawler->filter('html:contains("This value should have exactly 10 characters.")')->count()
        );
    }

    public function testNewActionFailureBinSerialBig()
    {

        //Create a client to go through the web page
        $client = static::createClient(array(), array('PHP_AUTH_USER' => 'admin', 'PHP_AUTH_PW'   => 'password'));
        $client->followRedirects(true);

        //Reques the contact add page
        $crawler = $client->request('POST','/oops/add');

        $form = $crawler->selectButton('Create OOPs Notice')->form();
        $form['form[binSerial]'] = 'testOOPs666';
        $form['form[problemType]'] = 'Damage';
        $form['form[description]'] = 'test oops description';
        $form['form[imageFile]'] = 'N;';

        $crawler = $client->submit($form);
        $this->assertGreaterThan(
        0,
        $crawler->filter('html:contains("This value should have exactly 10 characters.")')->count()
        );
    }


    public function testNewActionSuccessImageUploadPng()
    {
        //Create a client to go through the web page
        $client = static::createClient(array(), array('PHP_AUTH_USER' => 'admin', 'PHP_AUTH_PW'   => 'password'));
        $client->followRedirects(true);

        //Reques the contact add page
        $crawler = $client->request('POST','/oops/add');

        $form = $crawler->selectButton('Create OOPs Notice')->form();
        $form['form[binSerial]'] = 'testOOPs66';
        $form['form[problemType]'] = 'Damage';
        $form['form[description]'] = 'test oops description';
        $form['form[imageFile]'] = '../../app/Resources/images/OOPs NOTICE Valid1.png';

        $crawler = $client->submit($form);
        $this->assertGreaterThan(
        0,
        $crawler->filter('html:contains("OOPs Form Success!")')->count()
        );
    }

    /* NOT SURE HOW TO TEST BUT WORKS IN FORM
    public function testNewActionFailureImageUpload()
    {
        //Create a client to go through the web page
        $client = static::createClient(array(), array('PHP_AUTH_USER' => 'admin', 'PHP_AUTH_PW'   => 'password'));
        $client->followRedirects(true);


        //Reques the contact add page
        $crawler = $client->request('POST','/oops/add');

        $form = $crawler->selectButton('Create OOPs Notice')->form();
        $form['form[binSerial]'] = 'testOOPs66';
        $form['form[problemType]'] = 'Damage';
        $form['form[description]'] = 'test oops description';
        $form['form[imageFile]'] = '../../app/Resources/images/OOPs NOTICE inValid1.bmp';

        $crawler = $client->submit($form);
        $this->assertGreaterThan(
        0,
        $crawler->filter('html:contains("OOPs Form Success!")')->count()
        );
    }
    */


    /**
     * Tests that an OOPs notice can't be created with a description longer than 250 characters
     * Inputs: binSerial: testOOPs66
     *         problemType: Damage
     *         Description: 251 characters
     */
    public function testNewActionFailureDescriptionTooLong()
    {

        //Create a client to go through the web page
        $client = static::createClient(array(), array('PHP_AUTH_USER' => 'admin', 'PHP_AUTH_PW'   => 'password'));
        $client->followRedirects(true);

        //Reques the contact add page
        $crawler = $client->request('POST','/oops/add');

        $form = $crawler->selectButton('Create OOPs Notice')->form();
        $form['form[binSerial]'] = 'testOOPs66';
        $form['form[problemType]'] = 'Damage';
        $form['form[description]'] = 'iomnavmmoptwrwyvudipazflggbwzfhcigxjopzisfrpcieebmmhshofhpwvlzytxgnvzyhxejefjedyrwuvpzfswdxfwbrxmliujpcnjzzulm
foxxvpjekafmdmwewbzlxzldcdrvemyqnfppodwgrjveduviysaazeelmfbcksgrwfrnbfqogdyjflxeavrtdovifcmewcjhowycbnqprgkwgtdpoxmqjhxnnsbsqur
hskcweavnxumxqnomkdquqnpuaospigrznzngnrjsgnzmnejezwmrhqsiwgehfiqlhqcwwftfdlbsrfogxhnuykisnsfyhdnvnrkjbolbkhfsqeefuwbtkfbnvidhquu
isisczppkwnavzarusagtlywqocxktvlnudzpeouldjmrayuqtsqqxdd';
        $form['form[imageFile]'] = 'N;';

        $crawler = $client->submit($form);
        $this->assertGreaterThan(
        0,
        $crawler->filter('html:contains("Please enter a valid description with less than 250 characters")')->count()
        );
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