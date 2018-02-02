<?php
namespace AppBundle\DataFixtures\ORM;

use AppBundle\Entity\Route;
use AppBundle\Entity\Address;
use AppBundle\Entity\Container;
use AppBundle\Entity\Property;
use AppBundle\Entity\RoutePickup;

use AppBundle\DataFixtures\ORM\LoadContainerData;
use AppBundle\DataFixtures\ORM\LoadAddressData;
use AppBundle\DataFixtures\ORM\LoadPropertyData;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

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
        $container = (new Container())
            ->setContainerSerial("T35TSRL")
            ->setSize("6 yd")
            ->setType("Bin")
            ->setStatus("Active");

        // call the Constructor that will add a container to the database
        $containerFixtureLoader = new LoadContainerData($container);

        // add the address to the database
        $containerFixtureLoader->load($obMan);

        //Property Data

        // create a single address
        $address = (new Address())
            ->setStreetAddress("12 15th st east")
            ->setPostalCode("S0E 1A0")
            ->setCity("Saskatoon")
            ->setProvince("Saskatchewan")
            ->setCountry("Canada");

        $addressFixtureLoader = new LoadAddressData($address);
        $addressFixtureLoader->load($obMan);


        // create a single property
        $property = (new Property())
            ->setSiteId(6661489)
            ->setPropertyName("Charlton Legs")
            ->setPropertyType("House")
            ->setPropertyStatus("Active")
            ->setStructureId(54586)
            ->setNumUnits(10)
            ->setNeighbourhoodName("Sutherland")
            ->setNeighbourhoodId("O48")
            ->setAddress($address);

        $propertyFixtureLoader = new LoadContainerData($property);
        $propertyFixtureLoader->load($obMan);

        //generate route data
        //TODO:
        


        $obMan->persist($route);
        $obMan->flush();
    }
}