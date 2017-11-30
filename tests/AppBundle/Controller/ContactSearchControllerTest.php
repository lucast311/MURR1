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
    private $em;

    protected function setUp()
    {
        self::bootKernel();
        $this->em = static::$kernel->getContainer()
            ->get('doctrine')
            ->getManager();
    }

    public function testSuccessfullyReceiveSearch()
    {
        $repository = $this->em->getRepository(Contact::class);

        $client = static::createClient();

        $client->request('GET', '/contact/search/Jim');

        // query the database
        $repository->contactSearch("Jim");

        $this->assertContains('[{&quot;id&quot;:5,&quot;firstName&quot;:&quot;Jim&quot;,&quot;lastName&quot;:null,&quot;organization&quot;:null,&quot;primaryPhone&quot;:&quot;666-666-1234&quot;,&quot;phoneExtension&quot;:null,&quot;secondaryPhone&quot;:null,&quot;emailAddress&quot;:null,&quot;fax&quot;:null}]', $client->getResponse()->getContent());
    }

    public function testQueryTooLong()
    {
        $repository = $this->em->getRepository(Contact::class);

        $client = static::createClient();

        $client->request('GET', '/contact/search/BobJonesBobJonesBobJonesBobJonesBobJonesBobJonesBobJonesBobJonesBobJonesBobJonesBobJonesBobJonesBobJonesBobJonesBobJonesBobJonesBobJonesBobJonesBobJonesBobJonesBobJonesBobJonesBobJonesBobJonesBobJonesBobJonesBobJonesBobJonesBobJones');

        // query the database
        $repository->contactSearch("Jim");

        $this->assertContains('Query string was too long.', $client->getResponse()->getContent());
    }
}