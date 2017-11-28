<?php
namespace Tests\AppBundle\Controller;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use AppBundle\Entity\Contact;

/**
 * a test class for the search controller
 *
 * @version 1.0
 * @author cst206 cst225
 */
class ContactSearchControllerTest extends WebTestCase
{
    private $client;

    protected function setUp()
    {
        $this->client = static::createClient();
    }

    public function testSuccessfullyReceiveSearch()
    {
        // Get the repository
        $repository = $this->em->getRepository(Contact::class);

        // query the database
        $contacts = $repository->contactSearch("5");

        // Convert to a simple PHP object
        $testFormat = json_decode($contacts)[0];

        $this->assertEquals('{"id":1,"firstName":"AAAAAAAAAAAAAAAAAAAAA","lastName":"Jons","organization":null,"primaryPhone":null,"phoneExtention":null,"secondaryPhone":null,"emailAddress":"l@L.com","fax":null,"address":2}', $testFormat);
    }
}