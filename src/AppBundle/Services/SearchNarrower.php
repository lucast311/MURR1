<?php

namespace AppBundle\Services;

use AppBundle\Entity\Contact;
use AppBundle\Entity\Address;
/**
 * Searcher short summary.
 *
 * Searcher description.
 *
 * @version 1.0
 * @author CST225
 */
class SearchNarrower
{
    public function narrowContacts($searchResults, $cleanQuery)
    {
        $narrowedResults= array();
        $valuesFromResults = array();

        // an array to store the values of the returned objects
        $objectValues = array();
        $findMe = false;

        foreach ($searchResults as $result)
        {
            $methods = get_class_methods(get_class(new Contact()));

            // for each method in the entity you are searching for
            foreach ($methods as $method)
            {
                // check if the method is a getter
                if(strpos($method, 'get')===0)
                {
                    // check if the method is for the id
                    if(strpos($method, 'getId')===0)
                    {
                        // call getId and store its value in the array created above
                        $objectValues[] = $result->getId();
                    }
                    // check if the method is for the Address (remove this "else if" if you do not have a join in your entity)
                    else if(strpos($method, 'getAddress')===0)
                    {

                    }
                    else
                    {
                        // call the getter method and store the value returned
                        $objectValues[] = call_user_func([$result, $method]) == null ? 'null' : '"'.call_user_func([$result, $method]).'"';
                    }
                }
            }

            $methods = get_class_methods(get_class(new Address()));

            // foreach method in Address
            foreach ($methods as $method)
            {
                // check if the method is a getter
                if(strpos($method, 'get')===0)
                {
                    // check if the method is for the id
                    if(strpos($method, 'getId')===0)
                    {
                        // call getId and store its value in the array created above
                        $objectValues[] = $result->getId();
                    }
                    else if($result->getAddress() != null)
                    {
                        // call the getter method and store the value returned
                        $objectValues[] = call_user_func([$result->getAddress(), $method]) == null ? 'null' : '"'.call_user_func([$result->getAddress(), $method]).'"';
                    }
                }
            }

            // a variable to store the JSON formatted string
            $currData = '';

            // populate the JSON string with the values from the array of object values
            foreach($objectValues as $value)
            {
                $currData .= $value;
            }

            // a variable that will store the number of records returned
            $found = 0;

            // foreach separate string to query on in the passed in string
            foreach ($cleanQuery as $query)
            {
                // if the data to search for exists in the current record
                if(strpos($currData, $query) > 0)
                {
                    // increment found
                    $found++;
                }
            }

            // if the records returned match all of the passed in criteria to search for
            if($found == sizeof($cleanQuery))
            {
                $narrowedResults[] = $result;
                $findMe = true;
            }

            if($findMe)
            {
                $valuesFromResults[] = $objectValues;
            }

            $findMe = false;

            // Re-set the value of objectValues so that future loops don't append old data to it
            $objectValues = array();
        }

        $allData = array();

        $allData[] = $narrowedResults;
        $allData[] = $valuesFromResults;

        return $allData;
    }
}