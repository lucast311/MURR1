<?php
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * RouteControllerTest short summary.
 *
 * RouteControllerTest description.
 *
 * @version 1.0
 * @author cst244
 */
class RouteControllerTest extends WebTestCase
{

    private $em;

    protected function setUp()
    {
        self::bootKernel();
        $this->em = static::$kernel->getContainer()
            ->get('doctrine')
            ->getManager();
    }



    /**
     * (@inheritDoc)
     */
    protected function tearDown()
    {
        parent::tearDown();

    }

}