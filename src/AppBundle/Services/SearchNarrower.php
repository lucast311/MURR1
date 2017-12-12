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
    /**
     * A method that will narrow down any passed in search results so we only
     *  get back records that contain everything we wanted to find (only valid for Contact searches).
     * @param mixed $searchResults an array of all records initially returned from the query
     * @param mixed $cleanQuery an array of each string we wanted to find
     * @return array of narrowed search results
     */
    public function narrowContacts($searchResults, $cleanQuery)
    {
        // an array for the narrowed results
        $narrowedResults= array();

        // an array for the values for each of the narrowed results
        $valuesFromResults = array();

        // an array to store the values of the returned objects
        $objectValues = array();

        // foreach result in the passed in array of search results
        foreach ($searchResults as $result)
        {
            // get all methods in the contact class
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

            // re-set the $methods array witrh the Addresses methods
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

            // a variable to store the values of the current Entity
            $currData = '';

            // populate the $currdata string with the values from the array of object values
            foreach($objectValues as $value)
            {
                $currData .= $value;
            }

            // a variable that will store the number of $cleanQuery's the current record has
            $found = 0;

            // foreach separate string to query on in the passed in string
            foreach ($cleanQuery as $query)
            {
                // if the data to search for exists in the current record (check lowercase for case insensitive checks)
                if(strpos(strtolower($currData), strtolower($query)) > 0)
                {
                    // increment found
                    $found++;
                }
            }

            // if $found is equal the the size of the $cleanQuery array
            if($found == sizeof($cleanQuery))
            {
                // add the current record to the end of the array of narrowed searches
                $narrowedResults[] = $result;

                // add the current entities object values to the array of narrowed searches values
                $valuesFromResults[] = $objectValues;
            }

            // Re-set the value of $objectValues so that future loops don't append old data to look for
            $objectValues = array();
        }

        // an array to store all data gathered in this method
        $allData = array();

        // add both array's generated in this method to the array to return
        $allData[] = $narrowedResults;
        $allData[] = $valuesFromResults;

        // return the array of narrowed searches and the array of each searches object values
        return $allData;
    }
}