<?php
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * RoutePickupTest short summary.
 *
 * RoutePickupTest description.
 *
 * @version 1.0
 * @author cst244
 */
class RoutePickupTest extends KernelTestCase
{

    private $routePickup;

    public function setUp()
    {
        self::bootKernel();

        //$this->validator = Validation::createValidatorBuilder()->enableAnnotationMapping()->getValidator();
        // Gotta do some weird stuff because doing a validation on the class for unique
        $this->validator = static::$kernel->getContainer()->get("validator");
    }

}