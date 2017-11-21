<?php
namespace Tests\AppBundle\Repository;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;



/**
 * a test class for the search controller
 *
 * @version 1.0
 * @author cst206 cst225
 */
class ContactSearchRepositoryTest extends KernelTestCase
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
}