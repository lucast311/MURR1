<?php
namespace tests\AppBundle\Entity;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use AppBundle\Entity\CollectionHistory;
use AppBundle\Entity\Container;
use Symfony\Component\Validator\Validation;
use PHPUnit\Framework\TestCase;
use date;
use DateTime; 

/**
 * CollectionHistoryTest short summary.
 *
 * CollectionHistoryTest description.
 *
 * @version 1.0
 * @author cst201
 */
class CollectionHistoryTest extends KernelTestCase
{
    private $collectionHistory;
    private $validator;

    protected function setUp()
    {
        self::bootKernel();
        $this->collectionHistory = new CollectionHistory();
        $this->collectionHistory->setContainerId(1);
        $this->collectionHistory->setNotCollected(false);
        $this->collectionHistory->setNotes('Successfully pickedup');
        $this->collectionHistory->setDateCollected(new DateTime("2015-1-1"));

        $this->validator = static::$kernel->getContainer()->get("validator");

    }

    /**
     * 18a - test that a colection history can be valid
     */
    public function testCollectionHistoryAdded()
    {
        $error = $this->validator->validate($this->collectionHistory);
        $this->assertEquals(0, count($error));
    }

    /**
     * 18a - test that notCollected can only be boolean
     */
    public function testNotCollectedCanOnlyBeBoolean()
    {
        $notValidData = array( "Not Valid", 'c');
        foreach($notValidData as $tester)
        {
            //var_dump($tester);
            $this->collectionHistory->setNotCollected($tester);
            $error = $this->validator->validate($this->collectionHistory);
            $this->assertGreaterThan(0, count($error));
        }

        $this->collectionHistory->setNotCollected(true);
        $error = $this->validator->validate($this->collectionHistory);
        $this->assertEquals(0, count($error));
    }

    /**
     * 18a - sets the notes field to be a valid string
     */
    public function testNotesIsValid()
    {
        $this->collectionHistory->setNotes("This is valid");
        $error = $this->validator->validate($this->collectionHistory);
        $this->assertEquals(0, count($error));
    }

    /**
     * 18a - test that notes is valid at the boundry of 249/250
     */
    public function testNotesIsValidBoundary()
    {
        $this->collectionHistory->setNotes(str_repeat('a',249));
        $error = $this->validator->validate($this->collectionHistory);
        $this->assertEquals(0, count($error));
    }

    /**
     * 18a - test notes is valid at being exactly 250
     */
    public function testNotesIsValidExact()
    {
        $this->collectionHistory->setNotes(str_repeat('a',250));
        $error = $this->validator->validate($this->collectionHistory);
        $this->assertEquals(0, count($error));
    }

    /**
     * 18a - Test notes is invalid by being 1 over max, 251/250
     */
    public function testNotesIsInvalidBoundary()
    {
        $this->collectionHistory->setNotes(str_repeat('a',251));
        $error = $this->validator->validate($this->collectionHistory);
        $this->assertEquals(1, count($error));
    }

    /**
     * 18a - test notes is invalid length at 300 characters
     */
    public function testNotesIsInvalid()
    {
        $this->collectionHistory->setNotes(str_repeat('a',300));
        $error = $this->validator->validate($this->collectionHistory);
        $this->assertEquals(1, count($error));
    }

    /**
     * 18a - test that the date cannot be in the future
     */
    public function testDateCannotBeFuture()
    {
        $this->collectionHistory->setDateCollected(new DateTime('2020-1-1'));
        $error = $this->validator->validate($this->collectionHistory);
        $this->assertEquals(1, count($error));
    }
}