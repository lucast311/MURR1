<?php
namespace AppBundle\Repository;

use Doctrine\ORM\EntityRepository;
use AppBundle\Entity\Property;


class PropertyRepository extends EntityRepository
{
    /**
     * STORY 4A
     * Takes a property and inserts it into the database. The property
     * should have an address inside of it. Doctrine will automatically
     * associate keys and turn the address into a foreign key.
     * @param Property $property the property to insert
     * @return mixed the id of the new inserted property
     * @throws UniqueConstraintViolationException if ID already exists
     */
    public function save(Property $property)
    {
        // get the entity manager
        $em = $this->getEntityManager();
        // persist the new property in the database
        $em->persist($property);
        // get the address out of the property and persist it too
        $em->persist($property->getAddress());

        // flush them to the database
        $em->flush();

        //DO NOT CLOSE THE ENTITY MANAGER
        //The repo will not regenerate a new one
        //Close the entity manager
        //$em->close();
        //$em = null;
        // return the id of the new property in the database
        return $property->getId();
    }

    /**
     * Story 4d
     * Search through the database and check if any records contain any of
     *  the passed in strings (array of strings) in any of their fields.
     * @param mixed $queryStrings an array of strings to query the database on
     * @return array of searched entites returned from the queries
     */
    public function propertySearch($queryStrings)
    {
        // get the field names of both the Property and Address Entities.
        $propertyClassProperties = $this->getClassMetadata('AppBundle:Property')->fieldNames;
        $addressClassProperties = $this->getEntityManager()->getRepository('AppBundle:Address')->getClassMetadata()->fieldNames;

        $classPropertiesArray = array($propertyClassProperties, $addressClassProperties);

        $classNames = array('p', 'a');

        foreach ($classPropertiesArray as $array)
        {
            // shift off the id of each entity
        	array_shift($array);
        }

        $classPropertiesString = $this->searchHelper($classPropertiesArray, $queryStrings, $classNames);

        //// shift off the id from the Property (A user will never search based on this)
        //array_shift($propertyClassProperties);

        //// a variable to store the SQLite WHERE clause to query with
        //$searchStringProperties = '';
        //$searchStringAddresses = '';

        ////foreach field in the property
        //foreach($propertyClassProperties as $col=>$val)
        //{
        //    // foreach string to query on
        //    for ($i = 0; $i < sizeof($queryStrings); $i++)
        //    {
        //        // otherwise append to the WHERE clause while checking on lower case (this makes the search case insensitive)
        //        $searchStringProperties .= "LOWER(c.$val) LIKE '%{$queryStrings[$i]}%' OR ";
        //    }
        //}

        //// Remove the unneeded ' OR ' from the end of the query string
        //$searchStringProperties = rtrim($searchStringProperties, ' OR ');

        ////foreach field in the Address
        //foreach($addressClassProperties as $col=>$val)
        //{
        //    // foreach string to query on
        //    for ($i = 0; $i < sizeof($queryStrings); $i++)
        //    {
        //        // otherwise append to the WHERE clause while checking on lower case (this makes the search case insensitive)
        //        $searchStringAddresses .= "LOWER(a.$val) LIKE '%{$queryStrings[$i]}%' OR ";
        //    }
        //}

        //// Remove the unneeded ' OR ' from the end of the query string
        //$searchStringAddresses = rtrim($searchStringAddresses, ' OR ');

        //// set variables equal to the records returned from each of the two queries
        //$returnProperties = $this->getEntityManager()->createQuery(
        //   "SELECT c FROM AppBundle:Property c WHERE $searchStringProperties"
        //    )->getResult();

        //$returnAddresses = $this->getEntityManager()->createQuery(
        //   "SELECT c, a FROM AppBundle:Property c JOIN c.address a WHERE $searchStringAddresses"
        //    )->getResult();

        //// foreach address returned
        //foreach($returnAddresses as $returnAddress)
        //{
        //    // check if the address already exists in the array of contacts returned
        //    if(!in_array($returnAddress,$returnProperties))
        //    {
        //        // combine the search results
        //        $returnProperties[] = $returnAddress;
        //    }
        //}

        //// return the results
        //return $returnProperties;

        // The query that defines all the joins on communications to search for,
        //  and links them together based on id's
        return $this->getEntityManager()->createQuery(
        "SELECT c, p, a, co FROM AppBundle:Property p 
        LEFT OUTER JOIN AppBundle:Address a WITH p.address = a.id
        WHERE $classPropertiesString"
        )->getResult();

    }

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
                    // otherwise append to the WHERE clause while checking on lower case (this makes the search case insensitive)
                    $searchString .= "LOWER($class[$classCounter].$val) LIKE '%{$queryStrings[$i]}%' OR ";

                }
            }
            $classCounter++;
        }

        // Remove the unneeded ' OR ' from the end of the query string
        $searchString = rtrim($searchString, ' OR ');

        return $searchString;
    }

    ///**
    // * STORY 4h
    // * Takes an integer property id and returns an array of all containers associated
    // * with that property. The property id should be greater than 0.
    // * @param Property $property the property to find containers for
    // * @return mixed the array of containers associated with that property
    // */
    //public function getAllContainers(int $propertyId)
    //{

    //}
}