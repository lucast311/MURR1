<?php

namespace AppBundle\Services;


/**
 * cleaner short summary.
 *
 * cleaner description.
 *
 * @version 1.0
 * @author CST225
 */
class Cleaner
{
    /**
     * A method that will take in a string and return a clean array
     *  of values that can be used in search queries.
     * @param mixed $cleanMe the string to clean and break into an array
     * @return array made up each word from the intital $cleanMe string
     */
    public function cleanSearchQuery($cleanMe)
    {
        if(is_null($cleanMe)||$cleanMe == '+')  return array('');

        // an array to store the cleaned search queries with
        $cleanedResults = array();

        // an array to store the cleaned searches as after being converted to lower case
        $results = array();

        // replace all instances of a ',' with a ' '
        $cleanMe = str_replace(',', ' ', $cleanMe);

        // a variable set to the passed in string after being trimmed
        $queryString = trim($cleanMe);

        // while the string contains spaces
        while(strpos($queryString, '  '))
        {
            // remove the space character
            $queryString = str_replace('  ', ' ', $queryString);
        }

        // explode the now clean string into an array
        $cleanedResults = explode(' ', $queryString);

        // foreach string in the array
        foreach ($cleanedResults as $result)
        {
            // convert that string to lower case (used for case insensitive database checks)
            $results[] = $result;
        }

        // return the array of clean strings
        return $results;
    }
}