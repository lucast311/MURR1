<?php
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * RouteTest short summary.
 *
 * RouteTest description.
 *
 * @version 1.0
 * @author cst244
 */
class RouteTest extends KernelTestCase
{

    private $route;

    public function setUp()
    {
        self::bootKernel();

        //$this->validator = Validation::createValidatorBuilder()->enableAnnotationMapping()->getValidator();
        // Gotta do some weird stuff because doing a validation on the class for unique
        $this->validator = static::$kernel->getContainer()->get("validator");
    }

}