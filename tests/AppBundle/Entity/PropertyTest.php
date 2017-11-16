<?php

namespace tests\ApBundle\Entity;

use AppBundle\Entity\Property;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Validation;

class PropertyTest extends TestCase
{
    private $property;
    private $validator;

    public function setUp()
    {
        $this->property = new Property();
        
    }
}