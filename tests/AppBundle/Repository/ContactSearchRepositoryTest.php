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

    protected function setUp()
    {
        self::bootKernel();
        $this->em = static::$kernel->getContainer()
            ->get('doctrine')
            ->getManager();
    }



    public function testRemoveTrailingSpaces()
    {
        
    }
}