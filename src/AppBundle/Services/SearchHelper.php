<?php

namespace AppBundle\Services;

/**
 * SearchHelper short summary.
 *
 * SearchHelper description.
 *
 * @version 1.0
 * @author cst241
 */
class SearchHelper
{
    /**
     * Story 11c
     *
     * Function to help the repository search functions. Will create a string by looping
     *  through a class properties array which catains relevent entity information, and
     *  append the relevent query information to it. Then return the string.
     * @param mixed $classProperties
     * @param mixed $queryStrings
     * @param mixed $class
     * @return string
     */
    public function searchHelper($classPropertiesArray, $queryStrings, $class)
    {
        $classCounter = 0;
        $searchString = '';

        foreach ($classPropertiesArray as $classProperties)
        {
            //foreach field in the list of passed in claas properties
            foreach($classProperties as $col=>$val)
            {
                // foreach string to query on
                for ($i = 0; $i < sizeof($queryStrings); $i++)
                {
                    //if(array_search($classPropertiesArray[]))
                        // otherwise append to the WHERE clause while checking on lower case (this makes the search case insensitive)
                        $searchString .= "LOWER($class[$classCounter].$val) LIKE '%{$queryStrings[$i]}%' OR ";
                }
            }
            $classCounter++;
        }

        // Remove the unneeded ' OR ' from the end of the query string
        $searchString = rtrim($searchString, ' OR ');

        if($searchString == '') $searchString = "1 = 1";

        return $searchString;
    }
}