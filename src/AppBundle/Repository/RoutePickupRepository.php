<?php

namespace AppBundle\Repository;

/**
 * RoutePickupRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */

use AppBundle\Entity\RoutePickup;

class RoutePickupRepository extends \Doctrine\ORM\EntityRepository
{

    public function save(RoutePickup $routePickup){
        
    }

    public function updateOrders($routeId, $startAt, $increment=true){
        
    }
}
