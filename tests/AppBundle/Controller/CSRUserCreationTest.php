<?php

namespace Tests\AppBundle\Controller;

use AppBundle\Entity\OOPs;
use PHPUnit\Framework\TestCase;

/**
 * 13a_CSR_user_creation_test
 */
class CSRUserCreationTest extends TestCase
{
    //valid inputs
    public function testValidCreationProbContamination()
    {
        $testOOPs = new OOPs('1111111111', 'contanmination');
        $this->assertTrue($testOOPs->getProblem() === 'contanmination');
    }

    public function testValidCreationProbDamage()
    {
        $testOOPs = new OOPs('1111111111', 'damage');
        $this->assertTrue($testOOPs->getProblem() === 'damage');
    }

    public function testValidCreationProbBlocked()
    {
        $testOOPs = new OOPs('1111111111', 'blocked');
        $this->assertTrue($testOOPs->getProblem() === 'blocked');
    }

    public function testValidCreationDescription()
    {
        $testOOPs = new OOPs('1111111111', 'contamination', '', "Someone tossed garbage into the bin");
        $this->assertTrue(strlen($testOOPs->getDescription) > 0);
    }

    //i have no clue how to test this yet
    /*
    public function testValidCreationImage()
    {
        $testOOPs = new OOPs('1111111111', 'contamination', );
        $this->assertTrue(strlen($testOOPs->getDescription) > 0);
    }
    */

    public function testValidCreationStatusInProg()
    {
        $testOOPs = new OOPs('1111111111', 'contamination', 'In Progress');
        $this->assertTrue($testOOPs->getStatus() === 'In Progress');
    }

    public function testValidCreationStatusCompleted()
    {
        $testOOPs = new OOPs('1111111111', 'contamination', 'In Progress');
        $this->assertTrue($testOOPs->getStatus() === 'Completed');
    }

    //Image is a JPEG. Also no idea how We would test this

     //Valid Boundary tests
    public function testBoundarySerialNumLessThenMax()
    {
        $testOOPs = new OOPs('ZZZZZZZZZX ', 'contamination');
        $this->assertTrue(strlen($testOOPs->getBinSerial()) < 10);
    }

    public function testBoundarySerialNumEqualToMax()
    {
        $testOOPs = new OOPs('ZZZZZZZZZZ', 'contamination');
        $this->assertTrue($testOOPs->getBinSerial() === 'ZZZZZZZZZZ');
    }

    public function testBoundaryDescriptionOneLessMax()
    {
        $testOOPs = new OOPs('ZZZZZZZZZZ', 'contamination', '',
            "iomnavmmoptwrwyvudipazflggbwzfhcigxjopzisfrpcieebmmhshofhpwvlzytxgnvzyhxejefjedyrwuvpzfswdxfwbrxmliujpcnjzzulm
foxxvpjekafmdmwewbzlxzldcdrvemyqnfppodwgrjveduviysaazeelmfbcksgrwfrnbfqogdyjflxeavrtdovifcmewcjhowycbnqprgkwgtdpoxmqjhxnnsbsqur
hskcweavnxumxqnomkdquqnpuaospigrznzngnrjsgnzmnejezwmrhqsiwgehfiqlhqcwwftfdlbsrfogxhnuykisnsfyhdnvnrkjbolbkhfsqeefuwbtkfbnvidhquu
isisczppkwnavzarusagtlywqocxktvlnudzpeouldjmrayuqtsqqx");
        $this->assertTrue(strlen($testOOPs->getDescription()) >= 250);
    }

    public function testBoundaryDescriptionEqualToMax()
    {
        $testOOPs = new OOPs('ZZZZZZZZZZ', 'contamination', '',
            "iomnavmmoptwrwyvudipazflggbwzfhcigxjopzisfrpcieebmmhshofhpwvlzytxgnvzyhxejefjedyrwuvpzfswdxfwbrxmliujpcnjzzulm
foxxvpjekafmdmwewbzlxzldcdrvemyqnfppodwgrjveduviysaazeelmfbcksgrwfrnbfqogdyjflxeavrtdovifcmewcjhowycbnqprgkwgtdpoxmqjhxnnsbsqur
hskcweavnxumxqnomkdquqnpuaospigrznzngnrjsgnzmnejezwmrhqsiwgehfiqlhqcwwftfdlbsrfogxhnuykisnsfyhdnvnrkjbolbkhfsqeefuwbtkfbnvidhquu
isisczppkwnavzarusagtlywqocxktvlnudzpeouldjmrayuqtsqqxt");
        $this->assertTrue(strlen($testOOPs->getDescription()) == 250);
    }


}