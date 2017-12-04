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
    public function cleanSearchQuery($cleanMe)
    {
        $result = array();

        $cleanMe = str_replace(',', ' ', $cleanMe);

        // a variable set to the passed in string after being trimmed
        $queryString = trim($cleanMe);

        while(strpos($queryString, '  '))
        {
            $queryString = str_replace('  ', ' ', $queryString);
        }

        $result = explode(' ', $queryString);

        return $result;
    }
}