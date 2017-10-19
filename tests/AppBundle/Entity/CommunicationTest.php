<?php

use PHPUnit\Framework\TestCase;
use AppBundle\Entity\Communication;

class CommunicationTest extends TestCase
{
    public function testGetSet()
    {
        $com = new Communication();

        $com->setDate(new DateTime("2017-10-05"));
        $com->setType("phone");
        $com->__set("medium", "incoming");
        $com->__set("contact", 1);
        $com->__set("property", 1);
        $com->__set("category","container");
        $com->__set("description","Container has graffiti and needs to be cleaned. Action request made");

        $this->assertEquals($com->__get('id'),1);
        $this->assertEquals($com->__get('date'),"2017-10-05");
        $this->assertEquals($com->__get('type'),"phone");
        $this->assertEquals($com->__get('medium'),"incoming");
        $this->assertEquals($com->__get('contact'),1);
        $this->assertEquals($com->__get('property'),1);
        $this->assertEquals($com->__get('category'),"container");
        $this->assertEquals($com->__get('description'),"Container has graffiti and needs to be cleaned. Action request made");

        $this->assertNull($com->__get(""));
    }
}