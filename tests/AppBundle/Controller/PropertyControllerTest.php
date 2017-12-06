<?php
namespace Tests\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use AppBundle\Entity\Property;

class PropertyControllerTest extends WebTestCase
{
    /**
     *
     * This test will check that you can access the route, populate fields,
     * submit the form, view the success message, and that the fields will
     * be blank after a success
     */
    public function testFormSuccess()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/property/add');

        $form = $crawler->selectButton('Submit')->form();

        //set form values
        $form['property[siteId]'] = 1593843;
        $form['property[propertyName]'] = 'Charlton Arms';
        $form['property[propertyType]'] = 'Townhouse Condo';
        $form['property[propertyStatus]'] = 'Active';
        $form['property[structureId]'] = 54586;
        $form['property[numUnits]'] = 5;
        $form['property[neighbourhoodName]'] = 'Sutherland';
        $form['property[neighbourhoodId]'] = 'O48';
        $form['property[address][streetAddress]'] = '123 Main Street';
        $form['property[address][postalCode]'] = 'S7N 0R7';
        $form['property[address][city]'] = 'Saskatoon';
        $form['property[address][province]'] = 'Saskatchewan';
        $form['property[address][country]'] = 'Canada';

        //Remove the property from the database if it already exists so we can insert this one
        //$em = $client->getContainer()->get('doctrine.orm.entity_manager');
        //$stmt = $em->getConnection()->prepare('DELETE FROM Property WHERE id = 1593843');
        //$stmt->execute();
        //$em->close();



        $crawler = $client->submit($form);

        $this->assertContains("Successfully added property",$client->getResponse()->getContent());

        //Refresh the form because a new one was created after submission
        $form = $crawler->selectButton('Submit')->form();

        //test that all fields are now empty
        //$this->assertEmpty($form['communication[date][year]']->getValue());
        $this->assertEmpty($form['property[siteId]']-> getValue());
        $this->assertEmpty($form['property[propertyName]']-> getValue());
        $this->assertEmpty($form['property[propertyType]']-> getValue());
        $this->assertEmpty($form['property[propertyStatus]']-> getValue());
        $this->assertEmpty($form['property[structureId]']-> getValue());
        $this->assertEmpty($form['property[numUnits]']-> getValue());
        $this->assertEmpty($form['property[neighbourhoodName]']-> getValue());
        $this->assertEmpty($form['property[neighbourhoodId]']-> getValue());
        $this->assertEmpty($form['property[address][streetAddress]']-> getValue());
        $this->assertEmpty($form['property[address][postalCode]']-> getValue());
        $this->assertEquals($form['property[address][city]']-> getValue(),"Saskatoon");
        $this->assertEquals($form['property[address][province]']-> getValue(),"Saskatchewan");
        $this->assertEquals($form['property[address][country]']-> getValue(),"Canada");
    }

    /**
     * This test will submit the form and check that an error message is displayed
     */
    public function testErrorMessage()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/property/add');

        $form = $crawler->selectButton('Submit')->form();

        //set form values
        $form['property[siteId]'] = 1593843;
        $form['property[propertyName]'] = 'Charlton Arms';
        $form['property[propertyType]'] = 'Townhouse Condo';
        $form['property[propertyStatus]'] = 'Active';
        $form['property[structureId]'] = 54586;
        $form['property[numUnits]'] = 5;
        $form['property[neighbourhoodName]'] = '';
        $form['property[neighbourhoodId]'] = 'O48';
        $form['property[address][streetAddress]'] = '123 Main Street';
        $form['property[address][postalCode]'] = 'S7N 0R7';
        $form['property[address][city]'] = 'Saskatoon';
        $form['property[address][province]'] = 'Saskatchewan';
        $form['property[address][country]'] = 'Canada';

        $crawler = $client->submit($form);

        $this->assertContains("Please specify a neighbourhood name",$client->getResponse()->getContent());
    }

    /**
     * This test will submit the form and check that an error message is displayed
     */
    public function testsiteIdDuplicate()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/property/add');

        for ($i = 0; $i < 2; $i++)
        {

            $form = $crawler->selectButton('Submit')->form();

            //set form values
            $form['property[siteId]'] = 1593843;
            $form['property[propertyName]'] = 'Charlton Arms';
            $form['property[propertyType]'] = 'Townhouse Condo';
            $form['property[propertyStatus]'] = 'Active';
            $form['property[structureId]'] = 54586;
            $form['property[numUnits]'] = 5;
            $form['property[neighbourhoodName]'] = 'Sutherland';
            $form['property[neighbourhoodId]'] = 'O48';
            $form['property[address][streetAddress]'] = '123 Main Street';
            $form['property[address][postalCode]'] = 'S7N 0R7';
            $form['property[address][city]'] = 'Saskatoon';
            $form['property[address][province]'] = 'Saskatchewan';
            $form['property[address][country]'] = 'Canada';

            $crawler = $client->submit($form);
        }
        $this->assertContains("Site Id already exists",$client->getResponse()->getContent());
    }

    protected function tearDown()
    {
        parent::tearDown();

        // Delete all the things that were just inserted. Or literally everything.
        $client = static::createClient();
        $em = $client->getContainer()->get('doctrine.orm.entity_manager');
        $stmt = $em->getConnection()->prepare('DELETE FROM Property');
        $stmt->execute();
        $em->close();

    }
}