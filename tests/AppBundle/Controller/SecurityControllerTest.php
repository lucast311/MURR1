<?php
namespace Tests\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use AppBundle\Entity\User;
use AppBundle\DataFixtures\ORM\LoadUserData;

class SecurityControllerTest extends WebTestCase
{
    private $em;

    /**
     * (@inheritDoc)
     */
    protected function setUp()
    {
        self::bootKernel();
        $this->em = static::$kernel->getContainer()
            ->get('doctrine')
            ->getManager();

        $userLoader = new LoadUserData();
        $userLoader->load($this->em);
    }

    /**
     * (@inheritDoc)
     */
    protected function tearDown()
    {
        parent::tearDown();

        $stmt = $this->em->getConnection()->prepare("DELETE FROM User");
        $stmt->execute();

        $this->em->close();
        $this->em = null; //avoid memory meaks
    }
}