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
     public function ToJSON($searchResult, $objectValues)
     {
         $resultReflection = new \ReflectionClass(get_class($searchResult));
         $resultProperties = $resultReflection->getProperties();

         $resultPropertyNames = array();
         foreach ($resultProperties as $property)
         {
             $resultPropertyNames[] = $property->name;
         }

         // Convert the records returned into JSON format.
         $jsonEncodedSearch = '{';
         $i = 0;

         foreach($resultPropertyNames as $propertyName)
         {
             $jsonEncodedSearch .= '"'.$propertyName.'":'.$objectValues[$i].',';
             $i++;
         }

         $jsonEncodedSearch = substr($jsonEncodedSearch,0,strlen($jsonEncodedSearch)-1);

         $jsonEncodedSearch .= '},';

         return $jsonEncodedSearch;
     }
 }