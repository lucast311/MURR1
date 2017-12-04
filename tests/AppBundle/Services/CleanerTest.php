<?php

namespace Tests\AppBundle\Services;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use AppBundle\Services\Cleaner;

/**
 * a test class for the search controller
 *
 * @version 1.0
 * @author cst206 cst225
 */
class CleanerTest extends WebTestCase
{
    // The Cleaner Service
    private $cleaner;

    /**
     * Setup a Cleaner Service
     * {@inheritDoc}
     */
    protected function setUp()
    {
        $this->cleaner = new Cleaner();
    }

    public function testSingleWord()
    {
        $result = $this->cleaner->cleanSearchQuery('Bob');

        $queryStrings = array();
        $queryStrings[] = 'Bob';

        $this->assertEquals($queryStrings, $result);
    }

    public function testMultipleWords()
    {
        $result = $this->cleaner->cleanSearchQuery('Bob Jones');

        $queryStrings = array();
        $queryStrings[] = 'Bob';
        $queryStrings[] = 'Jones';

        $this->assertEquals($queryStrings, $result);
    }

    public function testLeadingSpaces()
    {
        $result = $this->cleaner->cleanSearchQuery('  Bob');

        $queryStrings = array();
        $queryStrings[] = 'Bob';

        $this->assertEquals($queryStrings, $result);
    }

    public function testTrailingSpaces()
    {
        $result = $this->cleaner->cleanSearchQuery('Bob  ');

        $queryStrings = array();
        $queryStrings[] = 'Bob';

        $this->assertEquals($queryStrings, $result);
    }

    public function testMultipleInnerSpaces()
    {
        $result = $this->cleaner->cleanSearchQuery('Bob   Jones');

        $queryStrings = array();
        $queryStrings[] = 'Bob';
        $queryStrings[] = 'Jones';

        $this->assertEquals($queryStrings, $result);
    }

    public function testCommaSpaces()
    {
        $result = $this->cleaner->cleanSearchQuery('Bob, Jones');

        $queryStrings = array();
        $queryStrings[] = 'Bob';
        $queryStrings[] = 'Jones';

        $this->assertEquals($queryStrings, $result);
    }

    public function testCommas()
    {
        $result = $this->cleaner->cleanSearchQuery('Bob,Jones');

        $queryStrings = array();
        $queryStrings[] = 'Bob';
        $queryStrings[] = 'Jones';

        $this->assertEquals($queryStrings, $result);
    }

    public function testTrailingCommas()
    {
        $result = $this->cleaner->cleanSearchQuery('Bob,,,,,,');

        $queryStrings = array();
        $queryStrings[] = 'Bob';

        $this->assertEquals($queryStrings, $result);
    }

    public function testLeadingCommas()
    {
        $result = $this->cleaner->cleanSearchQuery(',,,,,Bob');

        $queryStrings = array();
        $queryStrings[] = 'Bob';

        $this->assertEquals($queryStrings, $result);
    }
}