<?php
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * a test class for the search controller
 *
 * @version 1.0
 * @author cst206 cst225
 */
class ContactSearchRepositoryTest extends WebTestCase
{
    private $em;
    private $client;

    protected function setUp()
    {
        self::bootKernel();
        $this->em = static::$kernel->getContainer()
            ->get('doctrine')
            ->getManager();

        $this->client = static::createClient();
    }

    public function testSuccessfullyReceiveSearch()
    {

    }

    public function testSuccessfullyReceivedSearchWithSpecialCharacter()
    {

    }

    public function testNoSearchOnOnlySpaces()
    {

    }

    public function testRemoveTrailingSpaces()
    {

    }

    public function testRemoveLeadingSpaces()
    {

    }

    public function testRemoveSandwichSpaces()
    {

    }

    public function testRemoveUnneededSpaces()
    {

    }