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

    /**
     * test a single word
     */
    public function testSingleWord()
    {
        // a varaiable that stores the results from the cleaner
        $result = $this->cleaner->cleanSearchQuery('Bob');

        // an array with the values we expect it to have
        $queryStrings = array();
        $queryStrings[] = 'Bob';

        // assert that the two arrays are equal
        $this->assertEquals($queryStrings, $result);
    }

    /**
     * test multiple words
     */
    public function testMultipleWords()
    {
        // a varaiable that stores the results from the cleaner
        $result = $this->cleaner->cleanSearchQuery('Bob Jones');

        // an array with the values we expect it to have
        $queryStrings = array();
        $queryStrings[] = 'Bob';
        $queryStrings[] = 'Jones';

        // assert that the two arrays are equal
        $this->assertEquals($queryStrings, $result);
    }

    /**
     * test remove leading spaces
     */
    public function testLeadingSpaces()
    {
        // a varaiable that stores the results from the cleaner
        $result = $this->cleaner->cleanSearchQuery('  Bob');

        // an array with the values we expect it to have
        $queryStrings = array();
        $queryStrings[] = 'Bob';

        // assert that the two arrays are equal
        $this->assertEquals($queryStrings, $result);
    }

    /**
     * test remove trailing space characters
     */
    public function testTrailingSpaces()
    {
        // a varaiable that stores the results from the cleaner
        $result = $this->cleaner->cleanSearchQuery('Bob  ');

        // an array with the values we expect it to have
        $queryStrings = array();
        $queryStrings[] = 'Bob';

        // assert that the two arrays are equal
        $this->assertEquals($queryStrings, $result);
    }

    /**
     * test remove multiple space characters between words
     */
    public function testMultipleInnerSpaces()
    {
        // a varaiable that stores the results from the cleaner
        $result = $this->cleaner->cleanSearchQuery('Bob   Jones');

        // an array with the values we expect it to have
        $queryStrings = array();
        $queryStrings[] = 'Bob';
        $queryStrings[] = 'Jones';

        // assert that the two arrays are equal
        $this->assertEquals($queryStrings, $result);
    }

    /**
     * test separating the string into an array based on ', '
     */
    public function testCommaSpaces()
    {
        // a varaiable that stores the results from the cleaner
        $result = $this->cleaner->cleanSearchQuery('Bob, Jones');

        // an array with the values we expect it to have
        $queryStrings = array();
        $queryStrings[] = 'Bob';
        $queryStrings[] = 'Jones';

        // assert that the two arrays are equal
        $this->assertEquals($queryStrings, $result);
    }

    /**
     * test separating the string into an array based on ','
     */
    public function testCommas()
    {
        // a varaiable that stores the results from the cleaner
        $result = $this->cleaner->cleanSearchQuery('Bob,Jones');

        // an array with the values we expect it to have
        $queryStrings = array();
        $queryStrings[] = 'Bob';
        $queryStrings[] = 'Jones';

        // assert that the two arrays are equal
        $this->assertEquals($queryStrings, $result);
    }

    /**
     * test remove trailing comma characters
     */
    public function testTrailingCommas()
    {
        // a varaiable that stores the results from the cleaner
        $result = $this->cleaner->cleanSearchQuery('Bob,,,,,,');

        // an array with the values we expect it to have
        $queryStrings = array();
        $queryStrings[] = 'Bob';

        // assert that the two arrays are equal
        $this->assertEquals($queryStrings, $result);
    }

    /**
     * test remove leading comma characters
     */
    public function testLeadingCommas()
    {
        // a varaiable that stores the results from the cleaner
        $result = $this->cleaner->cleanSearchQuery(',,,,,Bob');

        // an array with the values we expect it to have
        $queryStrings = array();
        $queryStrings[] = 'Bob';

        // assert that the two arrays are equal
        $this->assertEquals($queryStrings, $result);
    }

    /**
     * test only space characters
     */
    public function testOnlySpaces()
    {
        // a varaiable that stores the results from the cleaner
        $result = $this->cleaner->cleanSearchQuery('    ');

        // an array with the values we expect it to have
        $queryStrings = array('');

        // assert that the two arrays are equal
        $this->assertEquals($queryStrings, $result);
    }

    /**
     * test no characters
     */
    public function testEmptyString()
    {
        // a varaiable that stores the results from the cleaner
        $result = $this->cleaner->cleanSearchQuery('');

        // an array with the values we expect it to have
        $queryStrings = array('');

        // assert that the two arrays are equal
        $this->assertEquals($queryStrings, $result);
    }
}