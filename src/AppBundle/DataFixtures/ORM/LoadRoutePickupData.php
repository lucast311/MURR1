<?php
namespace AppBundle\DataFixtures\ORM;

use AppBundle\Entity\Container;
use AppBundle\Entity\Route;
use AppBundle\Entity\Truck;
use AppBundle\Entity\Property;
use AppBundle\Entity\Address;
use AppBundle\Entity\RoutePickup;

use AppBundle\DataFixtures\ORM\LoadTruckData;

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
            $truck = (new Truck())
                ->setTruckId("000767")
                ->setType("TESTTRUCK1");

            $ctnr = (new Container())
                ->setContainerSerial("TESTCTNR1")
                ->setSize("TESTSIZE1")
                ->setType("Bin")
                ->setStatus("Active");
            $ctnrLoader = new LoadContainerData($ctnr);
            $ctnrLoader->load($obMan);

            $addr = (new Address())
                ->setStreetAddress("1 TEST ST")
                ->setPostalCode("S0E 1A0")
                ->setCity("TESTOON")
                ->setProvince("SASKATCHATEST")
                ->setCountry("TESTNADA");
            $addrLoader = new LoadAddressData($addr);
            $addrLoader->load($obMan);

            // create a single property
            $prop = (new Property())
                ->setSiteId(46029)
                ->setPropertyName("TESTAPT1")
                ->setPropertyType("Appartment")
                ->setPropertyStatus("Active")
                ->setStructureId(242424)
                ->setNumUnits(24)
                ->setNeighbourhoodName("TESTHOOD1")
                ->setNeighbourhoodId("024")
                ->setAddress($addr);
            $propertyFixtureLoader = new LoadContainerData($prop);
            $propertyFixtureLoader->load($obMan);


            //generate route data
            $route = (new Route())
                ->setRouteId(2424);

            //generate RoutePickup data
            $this->routePickup = (new RoutePickup())
                ->setRoute($route)
                ->setContainer($ctnr)
                ->setPickupOrder(1)
                ->setTruck($truck);
            $rPickupLoader = new LoadRoutePickupData($this->routePickup);
            $rPickupLoader->load($obMan);

            $truck->setPickups(array($this->routePickup));
            $tLoader = new LoadTruckData($truck);
            $tLoader->load($obMan);

            $route->setPickups(array($this->routePickup));
            (new LoadRouteData())->overLoad($obMan,$route);
        }
        else{
            $obMan->persist($this->routePickup);
            $obMan->flush();
        }

    }
}