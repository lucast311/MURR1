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
        $propertyClassProperties = $this->getClassMetadata('AppBundle:Property')->fieldNames;
        $addressClassProperties = $this->getClassMetadata('AppBundle:Address')->fieldNames;
        $contactClassProperties = $this->getClassMetadata('AppBundle:Contact')->fieldNames;
        $containerClassProperties = $this->getClassMetadata('AppBundle:Container')->fieldNames;

        $classPropertiesArray = array($communicationClassProperties, $propertyClassProperties,
            $addressClassProperties, $contactClassProperties, $containerClassProperties);

        foreach ($classPropertiesArray as $array)
        {
            // shift off the id of each entity
        	array_shift($array);
        }

        // a variable to store the SQLite WHERE clause to query with
        //$stringsForSearches = array($searchStringCommunication = '', $searchStringProperty = '',
            //$searchStringAddress = '', $searchStringContact = '', $searchStringContainer = '');

        $classPropertiesString = "";

        foreach ($classPropertiesArray as $classProperties)
        {
        	$classPropertiesString .= $this->searchHelper($classProperties, $queryStrings);
        }


        // this is the query we tested
        // remember to add a property to the database with the siteId 555, and
        //  a communication with the date 2018-01-01
        return $this->getEntityManager()->createQuery(
        "SELECT c, p, a, cp, co FROM AppBundle:Communication c
        LEFT OUTER JOIN AppBundle:Property p ON c.propertyId = p.id
        LEFT OUTER JOIN AppBundle:Address a ON p.addressId = a.id
        LEFT OUTER JOIN AppBundle:ContactProperty cp ON cp.property_id = p.id
        LEFT OUTER JOIN AppBundle:Contact co ON cp.contact_id = co.id
        WHERE $classPropertiesString"
        )->getResult();
    }

    public function searchHelper($classProperties, $queryStrings)
    {
        $searchString = '';

        //foreach field in the list of passed in claas properties
        foreach($classProperties as $col=>$val)
        {
            // foreach string to query on
            for ($i = 0; $i < sizeof($queryStrings); $i++)
            {
                // otherwise append to the WHERE clause while checking on lower case (this makes the search case insensitive)
                $searchString .= "LOWER(c.$val) LIKE '%{$queryStrings[$i]}%' OR ";
            }
        }

        // Remove the unneeded ' OR ' from the end of the query string
        $searchString = rtrim($searchString, ' OR ');

        return $searchString;
    }
}