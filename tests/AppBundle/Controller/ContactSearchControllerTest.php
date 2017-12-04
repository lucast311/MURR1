<?php
namespace Tests\AppBundle\Controller;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use AppBundle\Entity\Contact;
use AppBundle\Services\Changer;
use AppBundle\Services\SearchNarrower;

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

    //public function testSuccessfullyReceiveSearch()
    //{
    //    $repository = $this->em->getRepository(Contact::class);

    //    $client = static::createClient();

    //    $client->request('GET', '/contact/search/Jim');

    //    // query the database
    //    $repository->contactSearch("Jim");

    //    $this->assertContains('[{&quot;id&quot;:9,&quot;firstName&quot;:&quot;Jim&quot;,&quot;lastName&quot;:&quot;Jim&quot;,&quot;organization&quot;:null,&quot;primaryPhone&quot;:null,&quot;phoneExtention&quot;:null,&quot;secondaryPhone&quot;:null,&quot;emailAddress&quot;:null,&quot;fax&quot;:null,&quot;address&quot;:9}]', $client->getResponse()->getContent());
    //}

    //public function testQueryTooLong()
    //{
    //    $repository = $this->em->getRepository(Contact::class);

    //    $client = static::createClient();

    //    $client->request('GET', '/contact/search/BobJonesBobJonesBobJonesBobJonesBobJonesBobJonesBobJonesBobJonesBobJonesBobJonesBobJonesBobJonesBobJonesBobJonesBobJonesBobJonesBobJonesBobJonesBobJonesBobJonesBobJonesBobJonesBobJonesBobJonesBobJonesBobJonesBobJonesBobJonesBobJones');

    //    // query the database
    //    $repository->contactSearch("Jim");

    //    $this->assertContains('[{&quot;role&quot;:null}]', $client->getResponse()->getContent());
    //}

    //public function testChangerFunctionality()
    //{
    //    $changer = new Changer();
    //    $searchNarrower = new SearchNarrower();
    //    $repository = $this->em->getRepository(Contact::class);

    //    $client = static::createClient();

    //    $client->request('GET', '/contact/search/Jim');

    //    // query the database
    //    $results = $repository->contactSearch("Jim");

    //    $cleanQuery = array();
    //    $cleanQuery[] = 'Bob';
    //    $cleanQuery[] = 'Jones';

    //    $narrowedSearches = $searchNarrower->narrowContacts($results, $cleanQuery);

    //    $jsonFormat = $changer->ToJSON($results[0], $narrowedSearches[1][1]);

    //    $this->assertTrue($results != $jsonFormat);
    //}
}