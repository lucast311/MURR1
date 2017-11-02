<?php
namespace tests\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

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
    public function testNewActionSuccess()
    {

        //Create a client to go through the web page
        $client = static::createClient();
        $client->followRedirects(true);

        //Reques the contact add page
        $crawler = $client->request('POST','/oops/add');

        $form = $crawler->selectButton('Create OOPs Notice')->form();
        $form['form[binSerial]'] = 'testOOPs66';
        $form['form[problemType]'] = 'Damage';
        $form['form[description]'] = 'test oops description';
        $form['form[image]'] = 'N;';

        $crawler = $client->submit($form);
        $this->assertGreaterThan(
            0,
            $crawler->filter('html:contains("OOPs Form Success!")')->count()
            );

    }

    public function testNewActionFailureBinSerialSmall()
    {

        //Create a client to go through the web page
        $client = static::createClient();
        $client->followRedirects(true);

        //Reques the contact add page
        $crawler = $client->request('POST','/oops/add');

        $form = $crawler->selectButton('Create OOPs Notice')->form();
        $form['form[binSerial]'] = 'testOOPs6';
        $form['form[problemType]'] = 'Damage';
        $form['form[description]'] = 'test oops description';
        $form['form[image]'] = 'N;';

        $crawler = $client->submit($form);
        $this->assertGreaterThan(
        0,
        $crawler->filter('html:contains("This value should have exactly 10 characters.")')->count()
        );
    }

    public function testNewActionFailureBinSerialBig()
    {

        //Create a client to go through the web page
        $client = static::createClient();
        $client->followRedirects(true);

        //Reques the contact add page
        $crawler = $client->request('POST','/oops/add');

        $form = $crawler->selectButton('Create OOPs Notice')->form();
        $form['form[binSerial]'] = 'testOOPs666';
        $form['form[problemType]'] = 'Damage';
        $form['form[description]'] = 'test oops description';
        $form['form[image]'] = 'N;';

        $crawler = $client->submit($form);
        $this->assertGreaterThan(
        0,
        $crawler->filter('html:contains("This value should have exactly 10 characters.")')->count()
        );
    }


    public function testNewActionSuccessImageUpload()
    {
        //Create a client to go through the web page
        $client = static::createClient();
        $client->followRedirects(true);

        //Reques the contact add page
        $crawler = $client->request('POST','/oops/add');

        $form = $crawler->selectButton('Create OOPs Notice')->form();
        $form['form[binSerial]'] = 'testOOPs66';
        $form['form[problemType]'] = 'Damage';
        $form['form[description]'] = 'test oops description';
        $form['form[image]'] = 'N;';

        $crawler = $client->submit($form);
        $this->assertGreaterThan(
        0,
        $crawler->filter('html:contains("This value should have exactly 10 characters.")')->count()
        );
    }


    /**
     * Tests that an OOPs notice can't be created with a description longer than 250 characters
     * Inputs: binSerial: testOOPs66
     *         problemType: Damage
     *         Description: 251 characters
     */
    public function testNewActionFailureDescriptionTooLong()
    {

        //Create a client to go through the web page
        $client = static::createClient();
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
        $form['form[image]'] = 'N;';

        $crawler = $client->submit($form);
        $this->assertGreaterThan(
        0,
        $crawler->filter('html:contains("Please enter a valid description with less than 250 characters")')->count()
        );
    }

}