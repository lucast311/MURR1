<?php

namespace AppBundle\Repository;

use Doctrine\ORM\EntityRepository;
use AppBundle\Entity\Communication;

/**
 * This class will be responsible with interacting with the database
 * in relation to Communication objects
 */
class CommunicationRepository extends EntityRepository
{
    /**
     * This method will insert a single communication object into the database and
     * return its ID
     * @param Communication $communication
     * @return integer
     */
    public function insert(Communication $communication)
    {
        $em = $this->getEntityManager(); //get the entity manager to insert the communication
        $em->persist($communication); //prepare the object to be inserted (creates an ID)
        $em->flush(); //add to database

        $em->close(); //close communication
        $em = null; //set to null as a best practice

        return $communication->getId(); //return the set ID
    }

    /**
     * Story 11c
     * A function that will take in an array of strings that will be used to
     *  search for communication entities in the database.
     * @param array $queryString - an array of strings to query for
     * @return array of searched entites returned from the queries
     */
    public function communicationSearch($queryStrings)
    {
        // get the field names of both the Communication, Property, Address, Contact, and Container Entities.
        $communicationClassProperties = $this->getClassMetadata('AppBundle:Communication')->fieldNames;
        $propertyClassProperties = $this->getEntityManager()->getRepository('AppBundle:Property')->getClassMetadata()->fieldNames;
        $addressClassProperties = $this->getEntityManager()->getRepository('AppBundle:Address')->getClassMetadata()->fieldNames;
        $contactClassProperties = $this->getEntityManager()->getRepository('AppBundle:Contact')->getClassMetadata()->fieldNames;
        $containerClassProperties = $this->getEntityManager()->getRepository('AppBundle:Container')->getClassMetadata()->fieldNames;

        $classPropertiesArray = array($communicationClassProperties, $propertyClassProperties,
            $addressClassProperties, $contactClassProperties, $containerClassProperties);

        $classNames = array('c', 'p', 'a', 'co', 'con');

        foreach ($classPropertiesArray as $array)
        {
            // shift off the id of each entity
        	array_shift($array);
        }

        // a variable to store the SQLite WHERE clause to query with
        //$stringsForSearches = array($searchStringCommunication = '', $searchStringProperty = '',
            //$searchStringAddress = '', $searchStringContact = '', $searchStringContainer = '');

        $classPropertiesString = "";

        $classCounter = 0;
        foreach ($classPropertiesArray as $classProperties)
        {
        	$classPropertiesString .= $this->searchHelper($classProperties, $queryStrings, $classNames[$classCounter++]);
            var_dump($classPropertiesString);
        }

        // Remove the unneeded ' OR ' from the end of the query string
        $classPropertiesString = rtrim($classPropertiesString, ' OR ');

        var_dump(substr($classPropertiesString, 1400));
        // The query that defines all the joins on communications to search for,
        //  and links them together based on id's
        return $this->getEntityManager()->createQuery(
        "SELECT c, p, a, co FROM AppBundle:Communication c
        LEFT OUTER JOIN AppBundle:Property p WITH c.property = p.id
        LEFT OUTER JOIN AppBundle:Address a WITH p.address = a.id
        LEFT OUTER JOIN AppBundle:Contact co WITH co.properties = p.contacts
        LEFT OUTER JOIN AppBundle:Container con WITH con.property = p.id
        WHERE $classPropertiesString"
        )->getResult();




        //LEFT OUTER JOIN AppBundle:ContactProperty cp WITH cp.property_id = p.id
    }

    public function searchHelper($classProperties, $queryStrings, $class)
    {
        $searchString = '';

        //foreach field in the list of passed in claas properties
        foreach($classProperties as $col=>$val)
        {
            // foreach string to query on
            for ($i = 0; $i < sizeof($queryStrings); $i++)
            {
                // otherwise append to the WHERE clause while checking on lower case (this makes the search case insensitive)
                $searchString .= "LOWER($class.$val) LIKE '%{$queryStrings[$i]}%' OR ";
            }
        }

        return $searchString;
    }
}