<?php

namespace AppBundle\Repository;

/**
 * RouteRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */

use AppBundle\Entity\Route;
class RouteRepository extends \Doctrine\ORM\EntityRepository
{
    /**
     * Takes in a route and saves it into the database
     * @param Route $route the route to be saved
     * @return integer the ID of the saved route
     */
    public function save(Route $route)
    {
        $em = $this->getEntityManager();
        // persist the new contact in the database
        $em->persist($route);
        // flush them to the database
        $em->flush();
        //Close the entity manager
        // return the id of the new contact in the database
        return $route->getId();
    }


    /**
     * Story 22a
     * @param Route $route Route to be removed
     */
    public function remove(Route $route)
    {
        $em = $this->getEntityManager();
        // remove the route from the database
        $em->remove($route);
        // flush them from the database
        $em->flush();
        //Close the entity manager
        $em->close();
    }
}
