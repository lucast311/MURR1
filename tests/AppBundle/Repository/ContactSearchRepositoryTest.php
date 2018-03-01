<?php
namespace Tests\AppBundle\Repository;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use AppBundle\Entity\Contact;
use Tests\AppBundle\DatabasePrimer;

/**
 * a test class for the search controller
 *
 * @version 1.0
 * @author cst206 cst225
 */
class ContactSearchRepositoryTest extends KernelTestCase
{
    private $em;

    public static function setUpBeforeClass()
    {
        self::bootKernel();
        DatabasePrimer::prime(self::$kernel);
    }


    protected function setUp()
    {
        self::bootKernel();

        $this->em = static::$kernel->getContainer()
            ->get('doctrine')
            ->getManager();
    }

    //public function testSuccessfullyReceiveSearch()
    //{
    //    // Get the repository
    //    $repository = $this->em->getRepository(Contact::class);

    //    // query the database
    //    $contacts = $repository->contactSearch("Bob Jones");

    //    $this->assertEquals(3, sizeof($contacts));
    //}

    //public function testSuccessfullyReceivedSearchWithSpecialCharacter()
    //{
    //    // Get the repository
    //    $repository = $this->em->getRepository(Contact::class);

    //    // query the database
    //    $contacts = $repository->contactSearch("murr123@gmail.com");

    //    $this->assertEquals(2, sizeof($contacts));
    //}

    //public function testRemoveTrailingSpaces()
    //{
    //    // Get the repository
    //    $repository = $this->em->getRepository(Contact::class);

    //    // query the database
    //    $contacts = $repository->contactSearch("Bob ");

    //    $this->assertEquals(7, sizeof($contacts));
    //}

    //public function testRemoveLeadingSpaces()
    //{
    //    // Get the repository
    //    $repository = $this->em->getRepository(Contact::class);

    //    // query the database
    //    $contacts = $repository->contactSearch(" Bob");

    //    $this->assertEquals(7, sizeof($contacts));
    //}

    //public function testRemoveSandwichSpaces()
    //{
    //    // Get the repository
    //    $repository = $this->em->getRepository(Contact::class);

    //    // query the database
    //    $contacts = $repository->contactSearch("Bob   Jones");

    //    $this->assertEquals(3, sizeof($contacts));
    //}
}