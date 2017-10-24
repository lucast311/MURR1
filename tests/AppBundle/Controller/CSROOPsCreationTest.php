<?php

namespace Tests\AppBundle\Entity;

use AppBundle\Entity\OOPs;
use PHPUnit\Framework\TestCase;

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
        $testOOPs = new OOPs('1111111111', 'contanmination');
        $this->assertTrue($testOOPs != null);
        $this->assertTrue($testOOPs->getProblem() === 'contanmination');
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
        $this->assertTrue($testOOPs->getProblem() === 'damage');
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
        $this->assertTrue($testOOPs->getProblem() === 'blocked');
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
        $testOOPs = new OOPs('1111111111', 'contamination', '', "Someone tossed garbage into the bin");
        $this->assertTrue($testOOPs != null);
        $this->assertTrue(strlen($testOOPs->getDescription) > 0);
    }

    /**
     * Tests that a valid OOPs notice can be created using a simple binSerial and
     * a problem type of 'contamination' and a valid description, status, and image
     * Inputs: binSerial: 1111111111
     *         problemType: contanmination
     *         status: not started
     *         Description: Someone tossed garbage into the bin
     *         image: for testing, supplied from app/Resources/images
     */
    public function testValidImageUpload()
    {
        $validImageFile = '../../../app/Resources/images/OOPs NOTICE Valid1.png';
        //$validImageType = 'image/png';
        //header('Content-Type:'.$validImageType);
        //header('Content-Length: ' . filesize($validImageFile));
        $validImage = readfile($validImageFile);

        $testOOPs = new OOPs('ZZZZZZZZZ', 'contamination', 'not started', 'invalid image test', $validImage);
        $this->assertTrue($testOOPs != null);
        $this->assertTrue($testOOPs->getImage() != null);
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
        $testOOPs = new OOPs('ZZZZZZZZZX ', 'contamination');
        $this->assertTrue($testOOPs != null);
        $this->assertTrue(strlen($testOOPs->getBinSerial()) < 10);
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

    /**
     * Tests that a valid OOPs notice can be created using a simple binSerial and
     * a problem type of 'contamination' and a description of valid length
     * Inputs: binSerial: ZZZZZZZZZZ
     *         problemType: contanmination
     *         Description: 249 characters
     */
    public function testBoundaryDescriptionOneLessMax()
    {
        $testOOPs = new OOPs('ZZZZZZZZZZ', 'contamination', '',
            "iomnavmmoptwrwyvudipazflggbwzfhcigxjopzisfrpcieebmmhshofhpwvlzytxgnvzyhxejefjedyrwuvpzfswdxfwbrxmliujpcnjzzulm
foxxvpjekafmdmwewbzlxzldcdrvemyqnfppodwgrjveduviysaazeelmfbcksgrwfrnbfqogdyjflxeavrtdovifcmewcjhowycbnqprgkwgtdpoxmqjhxnnsbsqur
hskcweavnxumxqnomkdquqnpuaospigrznzngnrjsgnzmnejezwmrhqsiwgehfiqlhqcwwftfdlbsrfogxhnuykisnsfyhdnvnrkjbolbkhfsqeefuwbtkfbnvidhquu
isisczppkwnavzarusagtlywqocxktvlnudzpeouldjmrayuqtsqqx");
        $this->assertTrue($testOOPs != null);
        $this->assertTrue(strlen($testOOPs->getDescription()) >= 250);
    }

    /**
     * Tests that a valid OOPs notice can be created using a simple binSerial and
     * a problem type of 'contamination' and a description of valid length
     * Inputs: binSerial: ZZZZZZZZZZ
     *         problemType: contanmination
     *         Description: 250 characters
     */
    public function testBoundaryDescriptionEqualToMax()
    {
        $testOOPs = new OOPs('ZZZZZZZZZZ', 'contamination', '',
            "iomnavmmoptwrwyvudipazflggbwzfhcigxjopzisfrpcieebmmhshofhpwvlzytxgnvzyhxejefjedyrwuvpzfswdxfwbrxmliujpcnjzzulm
foxxvpjekafmdmwewbzlxzldcdrvemyqnfppodwgrjveduviysaazeelmfbcksgrwfrnbfqogdyjflxeavrtdovifcmewcjhowycbnqprgkwgtdpoxmqjhxnnsbsqur
hskcweavnxumxqnomkdquqnpuaospigrznzngnrjsgnzmnejezwmrhqsiwgehfiqlhqcwwftfdlbsrfogxhnuykisnsfyhdnvnrkjbolbkhfsqeefuwbtkfbnvidhquu
isisczppkwnavzarusagtlywqocxktvlnudzpeouldjmrayuqtsqqxt");
        $this->assertTrue($testOOPs != null);
        $this->assertTrue(strlen($testOOPs->getDescription()) == 250);
    }

    //Invalid tests

    /**
     * Tests that an attempted OOPs notice created with an invalid binSerial of
     * one too many characters will result in an error
     * Inputs: binSerial: ZZZZZZZZZZZ
     *         problemType: contanmination
     */
    public function testInvalidBoundarySerialNumberMoreThanMax()
    {
        $testOOPs = new OOPs('ZZZZZZZZZZZ', 'contamination');
        $this->assertTrue($testOOPs==null);
    }

    /**
     * Tests that an attempted OOPs notice created with an invalid binSerial of
     * one too few characters will result in an error
     * Inputs: binSerial: ZZZZZZZZZ
     *         problemType: contanmination
     */
    public function testInvalidBoundarySerialNumberLessThanMax()
    {
        $testOOPs = new OOPs('ZZZZZZZZZ', 'contamination');
        $this->assertTrue($testOOPs==null);
    }

    /**
     * Tests that an attempted OOPs notice created with an invalid Description with
     * a length of 251 characters
     * Inputs: binSerial: ZZZZZZZZZZ
     *         problemType: contanmination
     *         Description: 251 characters long
     */
    public function testInvalidBoundaryDescriptionMoreThanMax()
    {
        $testOOPs = new OOPs('ZZZZZZZZZZ', 'contamination', '',
    "iomnavmmoptwrwyvudipazflggbwzfhcigxjopzisfrpcieebmmhshofhpwvlzytxgnvzyhxejefjedyrwuvpzfswdxfwbrxmliujpcnjzzulm
foxxvpjekafmdmwewbzlxzldcdrvemyqnfppodwgrjveduviysaazeelmfbcksgrwfrnbfqogdyjflxeavrtdovifcmewcjhowycbnqprgkwgtdpoxmqjhxnnsbsqur
hskcweavnxumxqnomkdquqnpuaospigrznzngnrjsgnzmnejezwmrhqsiwgehfiqlhqcwwftfdlbsrfogxhnuykisnsfyhdnvnrkjbolbkhfsqeefuwbtkfbnvidhquu
isisczppkwnavzarusagtlywqocxktvlnudzpeouldjmrayuqtsqqxtl");
        $this->assertTrue($testOOPs==null);
    }

    /**
     * Tests that an attempted OOPs notice created with an invalid image will
     * fail to create an OOPs notice
     * Inputs: binSerial: ZZZZZZZZZZ
     *         problemType: contanmination
     *         Image: invalid image from app/Resource/images
     */
    public function testInvalidImageUpload()
    {
        $invalidImageFile = '../../../app/Resources/images/OOPs NOTICE inValid1.bmp';
        $invalidImageType = 'image/bmp';
        header('Content-Type:'.$invalidImageType);
        header('Content-Length: ' . filesize($invalidImageFile));
        $invalidImage = readfile($invalidImageFile);

        $testOOPs = new OOPs('ZZZZZZZZZ', 'contamination', 'not started', '', $invalidImage);
        $this->assertTrue($testOOPs==null);
    }


}