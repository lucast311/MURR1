<?php
namespace AppBundle\DataFixtures\ORM;

use AppBundle\Entity\Container;
use AppBundle\Entity\Route;
use AppBundle\Entity\RoutePickup;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class LoadRoutePickupData implements FixtureInterface
{
    public $routePickup;

    /**
     * Story22b Note: this constructor was added to allow loading individual properties
     * A constructor that sets the attribute the routePickup passed in
     * @param mixed $routePickup the property entity passed in
     */
    public function __construct($routePickup = null)
    {
        // set the routePickup attribute
        $this->routePickup = $routePickup;
    }

    /**
     * A fixture method to create routePickups in the database for testing
     * @param ObjectManager $obMan the object manager
     */
    public function load(ObjectManager $obMan)
    {
        if(is_null($this->routePickup))
        {
            //for custom auto-loads
        }

        $obMan->persist($this->routePickup);
        $obMan->flush();
    }
}