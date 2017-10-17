<?php

namespace Tests\AppBundle\Controller;

use AppBundle\Entity\OOPs;
use PHPUnit\Framework\TestCase;

/**
 * 13a_CSR_user_creation_test
 */
class CSROOPsCreationTest extends TestCase
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

    public function testValidImageUpload()
    {
        $validImageFile = '../../../../app/Resources/images/OOPs NOTICE Valid1.png';
        $validImageType = 'image/png';
        //header('Content-Type:'.$validImageType);
        //header('Content-Length: ' . filesize($validImageFile));
        $validImage = readfile($validImageFile);

        $testOOPs = new OOPs('ZZZZZZZZZ', 'contamination', 'not started', 'invalid image test', $validImage);
        $this->assertTrue($testOOPs->getImage() != null);
    }


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

    public function testInvalidBoundarySerialNumberMoreThanMax()
    {
        $testOOPs = new OOPs('ZZZZZZZZZZZ', 'contamination');
        $this->assertTrue($testOOPs==null);
    }

    public function testInvalidBoundarySerialNumberLessThanMax()
    {
        $testOOPs = new OOPs('ZZZZZZZZZ', 'contamination');
        $this->assertTrue($testOOPs==null);
    }

    public function testInvalidBoundaryDescriptionMoreThanMax()
    {
        $testOOPs = new OOPs('ZZZZZZZZZZ', 'contamination', '',
    "iomnavmmoptwrwyvudipazflggbwzfhcigxjopzisfrpcieebmmhshofhpwvlzytxgnvzyhxejefjedyrwuvpzfswdxfwbrxmliujpcnjzzulm
foxxvpjekafmdmwewbzlxzldcdrvemyqnfppodwgrjveduviysaazeelmfbcksgrwfrnbfqogdyjflxeavrtdovifcmewcjhowycbnqprgkwgtdpoxmqjhxnnsbsqur
hskcweavnxumxqnomkdquqnpuaospigrznzngnrjsgnzmnejezwmrhqsiwgehfiqlhqcwwftfdlbsrfogxhnuykisnsfyhdnvnrkjbolbkhfsqeefuwbtkfbnvidhquu
isisczppkwnavzarusagtlywqocxktvlnudzpeouldjmrayuqtsqqxtl");
        $this->assertTrue($testOOPs==null);
    }

    public function testInvalidImageUpload()
    {
        $invalidImageFile = '../../../app/Resources/images/OOPs NOTICE inValid1.bmp';
        $invalidImageType = 'image/bmp';
        header('Content-Type:'.$invalidImageType);
        header('Content-Length: ' . filesize($invalidImageFile));
        $invalidImage = readfile($invalidImageFile);

        $testOOPs = new OOPs('ZZZZZZZZZ', 'contamination', 'not started', 'invalid image test', $invalidImage);
        $this->assertTrue($testOOPs==null);
    }
}