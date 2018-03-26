<?php
namespace AppBundle\Services;

use Doctrine\ORM\EntityManager;

/**
 * This is a service class that is called from within the jsonSearchActions of controllers,
 *  and returns a list of the 10 most recently created/updated entities of whatever type they want.
 *
 * @version 1.0
 * @author CST225
 */
class RecentUpdatesHelper
{
    /**
     * This method will take in an EntityManager, and an Entitie's name, and return
     *  an array containing up to 10 records sorted based on the date they were created/updated.
     * @param EntityManager $em - An EntityManager
     * @param string $entity - The name of the Entity to search for. The syntax is: 'AppBundle:{entityName}'
     * @return array
     */
    public function tenMostRecent($em, $entity)
    {
        // create a QueryBuilder
        $qb = $em->createQueryBuilder();

        // create the search query
        $qb->select("e")
            ->from("$entity", "e")
            ->orderBy("e" . ".dateModified", "desc")
            ->setMaxResults(10);

        // execute the search query
        $results = $qb->getQuery()->getResult();

        // return the results
        return $results;
    }
}