<?php
namespace AppBundle\Repository;

use Doctrine\ORM\EntityRepository;
use AppBundle\Entity\Property;
use AppBundle\Services\SearchHelper;


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


        //try
        //{
             //flush them to the database
            $em->flush();
        //}
        //catch (UniqueConstraintViolationException $e)
        //{

        //}

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

        //Add all of the class properties arrays to one array
        $classPropertiesArray = array($propertyClassProperties, $addressClassProperties);

        //an array of abbreviations to be used in the query. These represent each join
        $classNames = array('p', 'a');

        // shift off the id of each entity
        foreach ($classPropertiesArray as $array)
        {
            array_shift($array);
        }

        //create a searchHelper instance
        $searchHelper = new SearchHelper();

        //call the searchHelper service to return the class properties string
        $classPropertiesString = $searchHelper->searchHelper($classPropertiesArray, $queryStrings, $classNames);

        // The query that defines all the joins on communications to search for,
        //  and links them together based on id's
        $records = $this->getEntityManager()->createQuery(
        "SELECT p, a FROM AppBundle:Property p
        LEFT OUTER JOIN AppBundle:Address a WITH p.address = a.id
        WHERE $classPropertiesString"
        )->getResult();

        // remove any NULL values from the array (NULL values are represented by non-propety objects)
        $records = array_filter($records);

        $propObjects = array();

        foreach ($records as $record)
        {
        	if(get_class($record) == "AppBundle\Entity\Property")
            {
                $propObjects[] = $record;
            }
        }

        return $propObjects;
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