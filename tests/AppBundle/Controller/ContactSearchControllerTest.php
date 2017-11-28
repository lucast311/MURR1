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
        //send search

        //check response
        $this->assertContains('{"id":"5"', $this->client->getResponse()->getContent());
    }

    //public function testSuccessfullyReceivedSearchWithSpecialCharacter()
    //{

    //}

    //public function testNoSearchOnOnlySpaces()
    //{

    //}

    //public function testRemoveTrailingSpaces()
    //{

    //}

    //public function testRemoveLeadingSpaces()
    //{

    //}

    //public function testRemoveSandwichSpaces()
    //{

    //}

    //public function testRemoveUnneededSpaces()
    //{

    //}
}