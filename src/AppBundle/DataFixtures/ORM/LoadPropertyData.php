<?php
namespace AppBundle\DataFixtures\ORM;

use AppBundle\Entity\Property;
use AppBundle\Entity\Address;
use AppBundle\DataFixtures\ORM\LoadAddressData;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class LoadPropertyData implements FixtureInterface
{
    /**
     * Story_4d Note: i commented out address stuff.. properties should have addresses, right?
     * A fixture method to create Properties in the database for testing
     * @param ObjectManager $obMan the object manager
     */
    public function load(ObjectManager $obMan)
    {
        // create 10 Properties with the following data
        for($i=0;$i<10;$i++)
        {
            /* property doesnt have an address yet
            //Address data
            $address = (new Address())
                ->setStreetAddress("Test ST")
                ->setPostalCode('T3S 3TS')
                ->setCity('Saskatoon')
                ->setProvince('Saskatchetest')
                ->setCountry('Testnada');

            // call the Constructor that will add an address to the database
            $addressFixtureLoader = new LoadAddressData($address);

            // add the address to the database
            $addressFixtureLoader->load($obMan);
            */

            // Property data
            $property = (new Property())
                ->setSiteId((3593843+$i))
                ->setPropertyName("Charlton Arms")
                ->setPropertyType("Townhouse Condo")
                ->setPropertyStatus("Active")
                ->setStructureId(54586)
                ->setNumUnits(5)
                ->setNeighbourhoodName("Sutherland")
                ->setNeighbourhoodId("O48");
                //->setAddress($address);


            // add the Property to the database
            $obMan->persist($property);

            // flush the database connection
            $obMan->flush();
        }

        // create 5 Properties with the following data
        for($i=0;$i<5;$i++)
        {
            /* property doesnt have an address yet
            $address = (new Address())
                ->setStreetAddress("12 15th st east")
                ->setPostalCode('S0E1A0')
                ->setCity('Saskatoon')
                ->setProvince('Saskatchewan')
                ->setCountry('Canada');

            $addressFixtureLoader = new LoadAddressData($address);
            $addressFixtureLoader->load($obMan);
            */

            $property = (new Property())
                ->setSiteId(2593843+$i)
                ->setPropertyName("Charlton Arms")
                ->setPropertyType("Townhouse Condo")
                ->setPropertyStatus("Active")
                ->setStructureId(54586)
                ->setNumUnits(5)
                ->setNeighbourhoodName("Sutherland")
                ->setNeighbourhoodId("O48");
            //->setAddress($address)

            $obMan->persist($property);
            $obMan->flush();
        }

        /* property doesnt have an address yet
        // create a single address
        $address = (new Address())
            ->setStreetAddress("12 15th st east")
            ->setPostalCode("S0E 1A0")
            ->setCity("Saskatoon")
            ->setProvince("Saskatchewan")
            ->setCountry("Canada")
            ->setAddress($address);

        $addressFixtureLoader = new LoadAddressData($address);
        $addressFixtureLoader->load($obMan);
        */


        // create a single property
        $property = (new Property())
            ->setSiteId(1593843)
            ->setPropertyName("Charlton Arms")
            ->setPropertyType("Townhouse Condo")
            ->setPropertyStatus("Active")
            ->setStructureId(54586)
            ->setNumUnits(5)
            ->setNeighbourhoodName("Sutherland")
            ->setNeighbourhoodId("O48");

        $obMan->persist($property);
        $obMan->flush();
    }
}