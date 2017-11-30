<?php

//uses



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
    public function cleanSearchQuery($cleanMe)
    {
        $result = array();

        // a variable set to the passed in string after being trimmed
        $queryString = trim($cleanMe);

        $queries = array();
        // Break apart the passed in string based on 'comma spaces'
        if(strpos($queryString, ', '))
        {
            $queries = explode(', ', $queryString);
        }
        // Break apart the passed in string based on 'comma's'
        else if(strpos($queryString, ','))
        {
            $queries = explode(',', $queryString);
        }
        // Break apart the passed in string based on 'spaces'
        else
        {
            $queries = explode(' ', $queryString);
        }

        foreach($queries as $index=>$string)
        {
            if($string != '')
            {
                $result[]=$queries[$index];
            }
        }

        return $result;
    }
}