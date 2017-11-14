<?php

namespace Tests\AppBundle\Entity;

use PHPUnit\Framework\TestCase;
use AppBundle\Entity\Communication;
use DateTime;

class CommunicationTest extends TestCase
{
    public function testGetSet()
    {
        $com = new Communication();

        //date to be added to the object
        $date = new DateTime("2017-10-05");

        $com->setDate($date);
        $com->setType("phone");
        $com->setMedium("incoming");
        $com->setContact(1);
        $com->setProperty(1);
        $com->setCategory("container");
        $com->setDescription("Container has graffiti and needs to be cleaned. Action request made");
        $com->setUser(1);

        $this->assertEquals($com->getId(),0); //id is default 0
        $this->assertEquals($com->getDate(), $date);
        $this->assertEquals($com->getType(),"phone");
        $this->assertEquals($com->getMedium(),"incoming");
        $this->assertEquals($com->getContact(),1);
        $this->assertEquals($com->getProperty(),1);
        $this->assertEquals($com->getCategory(),"container");
        $this->assertEquals($com->getDescription(),"Container has graffiti and needs to be cleaned. Action request made");
        $this->assertEquals($com->getUser(),1);
    }
}