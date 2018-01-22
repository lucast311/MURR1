<?php

 namespace AppBundle\Services;

 /**
  * Changer short summary.
  *
  * Changer description.
  *
  * @version 1.0
  * @author CST225
  */
 class Changer
 {
     /**
      * Converts entities to JSON string objects
      * @param mixed $searchResult the array of entity objects to convert to JSON
      * @param mixed $objectValues the values belonging to the objects in $searchResults
      * @return string
      */
     public function ToJSON($searchResult, $objectValues)
     {
         // Create a ReflectionClass object of whatever type the passed in through $searchResults
         $resultReflection = new \ReflectionClass(get_class($searchResult));

         // get an array of all the property names from the ReflectionClass object
         $resultProperties = $resultReflection->getProperties();

         // create an array to store the objects property names
         $resultPropertyNames = array();

         // foreach property in the array of property names
         foreach ($resultProperties as $property)
         {
             // add the property name to the array of property names
             $resultPropertyNames[] = $property->name;
         }

         // Create a variable that initially contains '{'
         $jsonEncodedSearch = '{';

         // a counter for indexing to the $objectValues with
         $i = 0;

         // foreach property name in the array of property names
         foreach($resultPropertyNames as $propertyName)
         {
             // append the data from the property names and its corresponding value to the JSON string
             $jsonEncodedSearch .= '"'.$propertyName.'":'.$objectValues[$i].',';

             // increment the counter
             $i++;
         }

         // remove the last ',' from the JSON string
         $jsonEncodedSearch = substr($jsonEncodedSearch,0,strlen($jsonEncodedSearch)-1);

         // add a '},' to the end of the last JSON string object
         $jsonEncodedSearch .= '},';

         // return the JSON string
         return $jsonEncodedSearch;
     }
 }