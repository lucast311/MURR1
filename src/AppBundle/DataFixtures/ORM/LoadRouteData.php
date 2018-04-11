<?php
namespace AppBundle\DataFixtures\ORM;

use AppBundle\Entity\Route;
use AppBundle\Entity\Address;
use AppBundle\Entity\Container;
use AppBundle\Entity\Property;
use AppBundle\Entity\RoutePickup;

use AppBundle\DataFixtures\ORM\LoadContainerData;
use AppBundle\DataFixtures\ORM\LoadAddressData;
use AppBundle\DataFixtures\ORM\LoadRoutePickupData;
use AppBundle\DataFixtures\ORM\LoadPropertyData;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

use AppBundle\Services\TemplateToRoute;

class LoadRouteData implements FixtureInterface
{
    /**
     * A fixture method to create Routes in the database for testing
     * @param ObjectManager $obMan the object manager
     */
    public function load(ObjectManager $obMan)
    {
        // create 2 Route with the following data

        // Container data
        $containers[] = (new Container())
            ->setContainerSerial("T35TSRL")
            ->setSize("6 yd")
            ->setType("Bin")
            ->setStatus("Active");

        // call the Constructor that will add a container to the database
        $containerFixtureLoader = new LoadContainerData($containers[0]);
        $containerFixtureLoader->load($obMan);

        $containers[] = (new Container())
            ->setContainerSerial("Z35TSR2")
            ->setSize("6 yd")
            ->setType("Bin")
            ->setStatus("Active");

        // call the Constructor that will add a container to the database
        $containerFixtureLoader = new LoadContainerData($containers[1]);
        $containerFixtureLoader->load($obMan);

        $containers[] = (new Container())
            ->setContainerSerial("5W4G8UX")
            ->setSize("4 yd")
            ->setType("Cart")
            ->setStatus("Active");

        // call the Constructor that will add a container to the database
        $containerFixtureLoader = new LoadContainerData($containers[2]);
        $containerFixtureLoader->load($obMan);


        //Property Data

        // create a single address
        $address = (new Address())
            ->setStreetAddress("144 8th st West")
            ->setPostalCode("S0E 1A0")
            ->setCity("Saskatoon")
            ->setProvince("Saskatchewan")
            ->setCountry("Canada");

        $addressFixtureLoader = new LoadAddressData($address);
        $addressFixtureLoader->load($obMan);


        // create a single property
        $property = (new Property())
            ->setSiteId(4206969)
            ->setPropertyName("King Swag Apts")
            ->setPropertyType("Appartment")
            ->setPropertyStatus("Active")
            ->setStructureId(696969)
            ->setNumUnits(42)
            ->setNeighbourhoodName("South Central")
            ->setNeighbourhoodId("O69")
            ->setAddress($address);

        $propertyFixtureLoader = new LoadContainerData($property);
        $propertyFixtureLoader->load($obMan);

        //generate route data
        $template = (new Route())
            ->setRouteId("Template 1")
            ->setTemplate();

        //generate RoutePickup data
        $routePickups[] = (new RoutePickup())
            ->setRoute($template)
            ->setContainer($containers[0])
            ->setPickupOrder(1);

        $routePickupFixtureLoader = new LoadRoutePickupData($routePickups[0]);
        $routePickupFixtureLoader->load($obMan);

        $routePickups[] = (new RoutePickup())
            ->setRoute($template)
            ->setContainer($containers[1])
            ->setPickupOrder(2);

        $routePickupFixtureLoader = new LoadRoutePickupData($routePickups[1]);
        $routePickupFixtureLoader->load($obMan);

        $template->setPickups($routePickups);


        $obMan->persist($template);
        $obMan->flush();

        (new TemplateToRoute($obMan))->templateToRoute($template,(new Route())->setRouteId(1001));
    }
}