<?php

namespace AppBundle\Services;

//use AppBundle\Entity\Contact;
//use AppBundle\Entity\Address;
//use AppBundle\Entity\Property;
//use AppBundle\Entity\Communication;
//use AppBundle\Entity\Container;
/**
 * TODO::
 * Searcher description.
 *
 * @version 1.0
 * @author CST225
 */
class SearchNarrower
{
    /**
     * This method will be the general narrower that will handle all the main narrower functionality.
     * Will narrow down any passed in search results so we only
     * get back records that contain everything we wanted to find.
     * @param mixed $entity - the type of entity that we are searching for. I think that this is redundant,
     *                          and we might be able to remove it when we implement QueryBuilder in the other searches.
     * @param array $searchResults - an array of all records initially returned from the query.
     * @param mixed $cleanQuery - an array of each string we wanted to find.
     * @return array - of narrowed search results
     */
    public function narrower($searchResults, $cleanQuery, $entity)
    {
        // an array for the narrowed results
        $narrowedResults= array();

        // foreach result in the passed in array of search results
        foreach ($searchResults as $result)
        {
            $entitiesToIgnore = array();

            // a variable to store the values of the record
            $recordData = '';

            // call a helper method to return the string of values from the current result
            $recordData .= $this->narrowerHelper($result, $entitiesToIgnore);

            // a variable to indicate the number of query strings that were found in the string of result data
            $found = 0;

            // foreach separate string to query on in the passed in string
            foreach ($cleanQuery as $query)
            {
                // if the data to search for exists in the current record (check lowercase for case insensitive checks)
                if(strlen($query) === 0 || (strpos(strtolower($recordData), strtolower($query)) > 0))
                {
                    // increment found
                    $found++;
                }
            }

            // if $found is equal the the size of the $cleanQuery array
            if($found == sizeof($cleanQuery))
            {

                // add the current record to the end of the array of narrowed searches
                if(get_class($result) === get_class($entity))
                {
                    $narrowedResults[] = $result;
                }
            }
        }
        // return the array of narrowed searches and the array of each searches object values
        return $narrowedResults;
    }

    /**
     * Story 11b
     *
     * Function that creates a string of the record data
     * @param mixed $result - the current Entity we are looking at
     * @param mixed $entitiesToIgnore - the array that acts as our base case. We pass it by reference
     *                          so we don't lose any values when we recurse back up.
     * @return string - of all the values for the current object, and any objects it's linked to.
     */
    public function narrowerHelper($result, &$entitiesToIgnore)
    {
        $entitiesToAccept = array("AppBundle\Entity\Communication", "AppBundle\Entity\Property", "AppBundle\Entity\Address", "AppBundle\Entity\Contact", "AppBundle\Entity\Container", "AppBundle\Entity\Structure", "Proxies\__CG__\AppBundle\Entity\Address");

        // an array of arrys to store the values of the returned objects
        $objectValues = array();

        // a variable to store the values of the current Entity
        $currData = '';

        $currEntity = get_class($result);

        if(!in_array($currEntity, $entitiesToIgnore))
        {
            // get all methods in the contact class
            $methods = get_class_methods($currEntity);

            if(!in_array($currEntity, $entitiesToIgnore))
            {
                $entitiesToIgnore[] = $currEntity;
            }

            // for each method in the entity you are searching for
            foreach ($methods as $method)
            {
                // check if the method is a getter
                if(strpos($method, 'get')===0)
                {
                    if(!is_null($result)) //added by austin, mightve broken something
                    {
                        // check if the method is for the id
                        if(strpos($method, 'getId')===0)
                        {
                            // call getId and store its value in the array created above
                            $currData .= '"'.$result->getId().'"';
                        }
                        // else check if the method returns a string, int, or null.
                        // if so save that value to an array of strings.
                        else if($type = call_user_func([$result, $method]))
                        {
                            switch($type)
                            {
                                case is_null($type):
                                    $currData .= 'null';
                                    break;
                                // int or string
                                case is_int($type):
                                case is_string($type):
                                    $currData .= '"'.$type.'"';
                                    break;
                                case is_object($type) && get_class($type) == "Doctrine\ORM\PersistentCollection" && !in_array(($type->toArray()), $entitiesToIgnore):
                                    $persistentEntities = $type->toArray();

                                    foreach ($persistentEntities as $entity)
                                    {
                                    	$currData .= $this->narrowerHelper($entity, $entitiesToIgnore);
                                    }

                                    if(!in_array($currEntity, $entitiesToIgnore))
                                    {
                                        $entitiesToIgnore[] = $currEntity;
                                    }
                                    break;
                                case is_object($type) && in_array(get_class($type), $entitiesToAccept):
                                    $currData .= $this->narrowerHelper($type, $entitiesToIgnore);
                                    break;
                                default:
                                    break;
                            }
                        }
                    }
                }
            }
        }

        // populate the $currdata string with the values from the array of entity object value arrays
        foreach($objectValues as $value)
        {
            $currData .= $value;
        }

        return $currData;
    }

    ///**
    // * A method that will narrow down any passed in search results so we only
    // *  get back records that contain everything we wanted to find (only valid for Contact searches).
    // * @param mixed $searchResults an array of all records initially returned from the query
    // * @param mixed $cleanQuery an array of each string we wanted to find
    // * @return array of narrowed search results
    // */
    //public function narrowContacts($searchResults, $cleanQuery)
    //{
    //    $gettersToAvoid = array('getAddress','getProperties','getContacts');

    //    // an array for the narrowed results
    //    $narrowedResults= array();

    //    // an array for the values for each of the narrowed results
    //    $valuesFromResults = array();

    //    // an array to store the values of the returned objects
    //    $objectValues = array();

    //    // foreach result in the passed in array of search results
    //    foreach ($searchResults as $result)
    //    {
    //        // get all methods in the contact class
    //        $methods = get_class_methods(get_class(new Contact()));

    //        // for each method in the entity you are searching for
    //        foreach ($methods as $method)
    //        {
    //            // check if the method is a getter
    //            if(strpos($method, 'get')===0)
    //            {
    //                // check if the method is for the id
    //                if(strpos($method, 'getId')===0)
    //                {
    //                    // call getId and store its value in the array created above
    //                    $objectValues[] = $result->getId();
    //                }
    //                // check if the method is for the Address (remove this "else if" if you do not have a join in your entity)
    //                else if(!in_array($method, $gettersToAvoid))
    //                {
    //                    // call the getter method and store the value returned
    //                    $objectValues[] = call_user_func([$result, $method]) == null ? 'null' : '"'.call_user_func([$result, $method]).'"';
    //                }
    //            }
    //        }

    //        // re-set the $methods array witrh the Addresses methods
    //        $methods = get_class_methods(get_class(new Address()));

    //        // foreach method in Address
    //        foreach ($methods as $method)
    //        {
    //            // check if the method is a getter
    //            if(strpos($method, 'get')===0)
    //            {
    //                // check if the method is for the id
    //                if(strpos($method, 'getId')===0)
    //                {
    //                    // call getId and store its value in the array created above
    //                    $objectValues[] = $result->getId();
    //                }
    //                else if($result->getAddress() != null)
    //                {
    //                    // call the getter method and store the value returned
    //                    $objectValues[] = call_user_func([$result->getAddress(), $method]) == null ? 'null' : '"'.call_user_func([$result->getAddress(), $method]).'"';
    //                }
    //            }
    //        }

    //        // a variable to store the values of the current Entity
    //        $currData = '';

    //        // populate the $currdata string with the values from the array of object values
    //        foreach($objectValues as $value)
    //        {
    //            $currData .= $value;
    //        }

    //        // a variable that will store the number of $cleanQuery's the current record has
    //        $found = 0;

    //        // foreach separate string to query on in the passed in string
    //        foreach ($cleanQuery as $query)
    //        {
    //            // if the data to search for exists in the current record (check lowercase for case insensitive checks)
    //            if(strpos(strtolower($currData), strtolower($query)) > 0)
    //            {
    //                // increment found
    //                $found++;
    //            }
    //        }

    //        // if $found is equal the the size of the $cleanQuery array
    //        if($found == sizeof($cleanQuery))
    //        {
    //            // add the current record to the end of the array of narrowed searches
    //            $narrowedResults[] = $result;

    //            // add the current entities object values to the array of narrowed searches values
    //            $valuesFromResults[] = $objectValues;
    //        }

    //        // Re-set the value of $objectValues so that future loops don't append old data to look for
    //        $objectValues = array();
    //    }

    //    // an array to store all data gathered in this method
    //    $allData = array();

    //    // add both array's generated in this method to the array to return
    //    $allData[] = $narrowedResults;
    //    $allData[] = $valuesFromResults;

    //    // return the array of narrowed searches and the array of each searches object values
    //    return $allData;
    //}


    ///**
    // * Story_4d // basically the same as narrowProperties
    // * A method that will narrow down any passed in search results so we only
    // *  get back records that contain everything we wanted to find (only valid for Property searches).
    // * @param mixed $searchResults an array of all records initially returned from the query
    // * @param mixed $cleanQuery an array of each string we wanted to find
    // * @return array of narrowed search results
    // */
    //public function narrowProperties($searchResults, $cleanQuery)
    //{
    //    $gettersToAvoid = array('getAddress','getStatuses','getTypes','getContacts', 'getBins');

    //    // an array for the narrowed results
    //    $narrowedResults= array();

    //    // an array for the values for each of the narrowed results
    //    $valuesFromResults = array();

    //    // an array to store the values of the returned objects
    //    $objectValues = array();

    //    // foreach result in the passed in array of search results
    //    foreach ($searchResults as $result)
    //    {
    //        // get all methods in the property class
    //        $methods = get_class_methods(get_class(new Property()));

    //        // for each method in the entity you are searching for
    //        foreach ($methods as $method)
    //        {
    //            // check if the method is a getter
    //            if(strpos($method, 'get')===0)
    //            {
    //                // check if the method is for the id
    //                if(strpos($method, 'getId')===0)
    //                {
    //                    // call getId and store its value in the array created above
    //                    $objectValues[] = $result->getId();
    //                }
    //                // check if the method is for the Address (remove this "else if" if you do not have a join in your entity)
    //                else if(!in_array($method, $gettersToAvoid))
    //                {
    //                    // call the getter method and store the value returned
    //                    $objectValues[] = call_user_func([$result, $method]) == null ? 'null' : '"'.call_user_func([$result, $method]).'"';
    //                }
    //            }
    //        }

    //        // re-set the $methods array witrh the Addresses methods
    //        $methods = get_class_methods(get_class(new Address()));

    //        // foreach method in Address
    //        foreach ($methods as $method)
    //        {
    //            // check if the method is a getter
    //            if(strpos($method, 'get')===0)
    //            {
    //                // check if the method is for the id
    //                if(strpos($method, 'getId')===0)
    //                {
    //                    // call getId and store its value in the array created above
    //                    $objectValues[] = $result->getId();
    //                }
    //                else if($result->getAddress() != null)
    //                {
    //                    // call the getter method and store the value returned
    //                    $objectValues[] = call_user_func([$result->getAddress(), $method]) == null ? 'null' : '"'.call_user_func([$result->getAddress(), $method]).'"';
    //                }
    //            }
    //        }

    //        // a variable to store the values of the current Entity
    //        $currData = '';

    //        // populate the $currdata string with the values from the array of object values
    //        foreach($objectValues as $value)
    //        {
    //            $currData .= $value;
    //        }

    //        // a variable that will store the number of $cleanQuery's the current record has
    //        $found = 0;

    //        // foreach separate string to query on in the passed in string
    //        foreach ($cleanQuery as $query)
    //        {
    //            // if the data to search for exists in the current record (check lowercase for case insensitive checks)
    //            if(strpos(strtolower($currData), strtolower($query)) > 0)
    //            {
    //                // increment found
    //                $found++;
    //            }
    //        }

    //        // if $found is equal the the size of the $cleanQuery array
    //        if($found == sizeof($cleanQuery))
    //        {
    //            // add the current record to the end of the array of narrowed searches
    //            $narrowedResults[] = $result;

    //            // add the current entities object values to the array of narrowed searches values
    //            $valuesFromResults[] = $objectValues;
    //        }

    //        // Re-set the value of $objectValues so that future loops don't append old data to look for
    //        $objectValues = array();
    //    }

    //    // an array to store all data gathered in this method
    //    $allData = array();

    //    // add both array's generated in this method to the array to return
    //    $allData[] = $narrowedResults;
    //    $allData[] = $valuesFromResults;

    //    // return the array of narrowed searches and the array of each searches object values
    //    return $allData;
    //}

    ///**
    // * Story11c
    // * A helper method that will narrow down any passed in search results so we only
    // *  get back records that contain everything we wanted to find (only valid for Communication searches).
    // * @param array $searchResults an array of all records initially returned from the query
    // * @param array $cleanQuery an array of each string we wanted to find
    // * @return array of narrowed search results
    // */
    //public function narrowCommunications($searchResults, $cleanQuery)
    //{

    //}
}