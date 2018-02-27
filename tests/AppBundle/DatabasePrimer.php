<?php

namespace Tests\AppBundle;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\SchemaTool;
use Symfony\Component\HttpKernel\KernelInterface;

class DatabasePrimer
{
    public static $entityManager;

    public static function setKernel(){
        //set the kernel dir for tests
        $_SERVER["KERNEL_DIR"] = "./app";
    }

    public static function prime(KernelInterface $kernel)
    {
        // Make sure we are in the test environment
        if ('test' !== $kernel->getEnvironment()) {
            throw new \LogicException('Primer must be executed in the test environment');
        }


        // Get the entity manager from the service container
        self::$entityManager = $kernel->getContainer()->get('doctrine.orm.entity_manager');


        // Run the schema update tool using our entity metadata
        $metadatas = self::$entityManager->getMetadataFactory()->getAllMetadata();
        $schemaTool = new SchemaTool(self::$entityManager);
        $schemaTool->dropSchema($metadatas); //drop all data
        $schemaTool->updateSchema($metadatas); //update schema

        //Load any wanted fixtures: (blank for now)
    }
}
