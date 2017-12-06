<?php

namespace Tests\AppBundle\Entity;

use AppBundle\Entity\OOPs;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Validation;

/**
 * 13a_CSR_user_creation_test
 */

class CSROOPsCreationTest extends TestCase
{

    //valid inputs
    /**
     * Tests that a valid OOPs notice can be created using a simple binSerial and
     * a problem type of 'contamination'
     * Inputs: binSerial: 1111111111
     *         problemType: contanmination
     */
    public function testValidCreationProbContamination()
    {
        $testOOPs = new OOPs('1111111111','contamination');
        //$this->assertTrue($testOOPs != null);
        $this->assertTrue($testOOPs->getProblemType() === 'contamination');
    }

    /**
     * Tests that a valid OOPs notice can be created using a simple binSerial and
     * a problem type of 'damage'
     * Inputs: binSerial: 1111111111
     *         problemType: damage
     */
    public function testValidCreationProbDamage()
    {
        $testOOPs = new OOPs('1111111111', 'damage');
        $this->assertTrue($testOOPs != null);
        $this->assertTrue($testOOPs->getProblemType() === 'damage');
    }

    /**
     * Tests that a valid OOPs notice can be created using a simple binSerial and
     * a problem type of 'blocked'
     * Inputs: binSerial: 1111111111
     *         problemType: blocked
     */
    public function testValidCreationProbBlocked()
    {
        $testOOPs = new OOPs('1111111111', 'blocked');
        $this->assertTrue($testOOPs != null);
        $this->assertTrue($testOOPs->getProblemType() === 'blocked');
    }

    /**
     * Tests that a valid OOPs notice can be created using a simple binSerial and
     * a problem type of 'contamination' and a valid description
     * Inputs: binSerial: 1111111111
     *         problemType: contanmination
     *         Description: Someone tossed garbage into the bin
     */
    public function testValidCreationDescription()
    {
        $testOOPs = new OOPs('1111111111', 'contamination','', "Someone tossed garbage into the bin");
        $this->assertTrue($testOOPs != null);
        $this->assertTrue(strlen($testOOPs->getDescription()) > 0);
    }


    /**
     * Tests that a valid OOPs notice can be created using a simple binSerial and
     * a problem type of 'contamination' and a status of 'in Progress'
     * Inputs: binSerial: 1111111111
     *         problemType: contanmination
     *         status: in progress
     */
    public function testValidCreationStatusInProg()
    {
        $testOOPs = new OOPs('1111111111', 'contamination', 'In Progress');
        $this->assertTrue($testOOPs != null);
        $this->assertTrue($testOOPs->getStatus() === 'In Progress');
    }

    /**
     * Tests that a valid OOPs notice can be created using a simple binSerial and
     * a problem type of 'contamination' and a status of 'Completed'
     * Inputs: binSerial: 1111111111
     *         problemType: contanmination
     *         status: Completed
     */
    public function testValidCreationStatusCompleted()
    {
        $testOOPs = new OOPs('1111111111', 'contamination', 'Completed');
        $this->assertTrue($testOOPs != null);
        $this->assertTrue($testOOPs->getStatus() === 'Completed');
    }

     //Valid Boundary tests

    /**
     * Tests that a valid OOPs notice can be created using a simple binSerial and
     * a problem type of 'contamination' and a status of 'in Progress'
     * Inputs: binSerial: ZZZZZZZZZX
     *         problemType: contanmination
     */
    public function testBoundarySerialNumLessThenMax()
    {
        $testOOPs = new OOPs('ZZZZZZZZZX', 'contamination');
        //$this->assertTrue($testOOPs != null);
        $this->assertTrue(strlen($testOOPs->getBinSerial()) == 10);
    }

    /**
     * Tests that a valid OOPs notice can be created using a simple binSerial and
     * a problem type of 'contamination' and a status of 'in Progress'
     * Inputs: binSerial: ZZZZZZZZZZ
     *         problemType: contanmination
     */
    public function testBoundarySerialNumEqualToMax()
    {
        $testOOPs = new OOPs('ZZZZZZZZZZ', 'contamination');
        $this->assertTrue($testOOPs != null);
        $this->assertTrue($testOOPs->getBinSerial() === 'ZZZZZZZZZZ');
    }



}