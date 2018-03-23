<?php

namespace AppBundle\Services;

use Doctine\ORM\EntityManager;

/**
 *This is a service class that is called from within the json searchActions of controllers,
 * and returns a list of the 10 most recently created/updated entities of whatever type they want.
 *
 * @version 1.0
 * @author cst241
 */
class RecentUpdatesHelper
{
    /**
     * Story 12g
     * This method will take in an EntityManager, and an Entity's name and return
     *  an array containing up to 10 records sorted based on the date they were created/updated
     * @param EntityManager $em - an entity manager
     * @param string $entity - the name of the entity to search for. the syntax is: 'appbundle:{entityName}'
     * @return array
     */
    public function tenMostRecent($em, $entity)
    {
        //create a queryBuilder
        $qb = $em->createQueryBuilder();

        //create the search query
        $qb->select("e")
            ->from("$entity", "e")
            ->orderBy("e" . ".dateModified", "desc")
            ->setMaxResults(10);

        //execute the search query
        $results = $qb->getQuery()->getResults();

        return $results;
    }
}