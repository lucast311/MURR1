<?php

namespace Tests\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class DefaultControllerTest extends WebTestCase
{


    public function testAdd()
    {
        static::$kernal = static::createKernel();
        static::$kernal -> boot();
        $this->em = static::$kernel->getContainer()->get('doctrine')->getEntityManager();

    }
}