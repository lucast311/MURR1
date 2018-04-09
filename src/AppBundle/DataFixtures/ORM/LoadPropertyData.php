<?php
namespace AppBundle\DataFixtures\ORM;

use AppBundle\Entity\Property;
use AppBundle\Entity\Address;
use AppBundle\Entity\Contact;
use AppBundle\DataFixtures\ORM\LoadAddressData;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\Collections\ArrayCollection;

class LoadPropertyData implements FixtureInterface
{
    public $property;

    /**
     * Story22b Note: this constructor was added to allow loading individual properties
     * A constructor that sets the attribute the property passed in
     * @param mixed $property the property entity passed in
     */
    public function __construct($property = null)
    {
        // set the property attribute
        $this->property = $property;
    }

    /**
     * Story_4d Note: i commented out address stuff.. properties should have addresses, right?
     * A fixture method to create Properties in the database for testing
     * @param ObjectManager $obMan the object manager
     */
    public function load(ObjectManager $obMan)
    {
        if(is_null($this->property))
        {
            // create 10 Properties with the following data
            for($i=0;$i<9;$i++)
            {
                //Address data
                $address = (new Address())
                    ->setStreetAddress("Test ST")
                    ->setPostalCode('T3S 3TS')
                    ->setCity('Saskatoon')
                    ->setProvince('Saskatchetest')
                    ->setCountry('Testnada');

                //contact data
                $contact1 = (new Contact())
                    ->setFirstName("Ken")
                    ->setLastName("Kenson")
                    ->setRole("Property Manager");
                $contact2 = (new Contact())
                    ->setFirstName("Matt")
                    ->setLastName("Mattson")
                    ->setRole("Property Manager");
                $contacts = array($contact1, $contact2);

                // call the Constructor that will add an address to the database
                $addressFixtureLoader = new LoadAddressData($address);

                // add the address to the database
                $addressFixtureLoader->load($obMan);

                $contactAC = new ArrayCollection($contacts);

                // Property data
                $this->property = (new Property())
                    ->setSiteId((3593843+$i))
                    ->setPropertyName("Charlton Arms")
                    ->setPropertyType("Townhouse Condo")
                    ->setPropertyStatus("Active")
                    ->setStructureId(54586)
                    ->setNumUnits(5)
                    ->setNeighbourhoodName("Sutherland")
                    ->setNeighbourhoodId("O48")
                    ->setAddress($address);

                $this->property->setContacts($contactAC); 

                // add the Property to the database
                $obMan->persist($this->property);

                // flush the database connection
                $obMan->flush();
            }

            // create 5 Properties with the following data
            for($i=0;$i<5;$i++)
            {
                $address = (new Address())
                    ->setStreetAddress("12 15th st east")
                    ->setPostalCode('S0E1A0')
                    ->setCity('Saskatoon')
                    ->setProvince('Saskatchewan')
                    ->setCountry('Canada');

                $addressFixtureLoader = new LoadAddressData($address);
                $addressFixtureLoader->load($obMan);


                $this->property = (new Property())
                    ->setSiteId(2593843+$i)
                    ->setPropertyName("Charlton Arms")
                    ->setPropertyType("Townhouse Condo")
                    ->setPropertyStatus("Active")
                    ->setStructureId(54586)
                    ->setNumUnits(5)
                    ->setNeighbourhoodName("Sutherland")
                    ->setNeighbourhoodId("O48")
                    ->setAddress($address);

                $obMan->persist($this->property);
                $obMan->flush();
            }


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
            $this->property = (new Property())
                ->setSiteId(6661488)
                ->setPropertyName("Charlton Arms")
                ->setPropertyType("Townhouse Condo")
                ->setPropertyStatus("Active")
                ->setStructureId(54586)
                ->setNumUnits(5)
                ->setNeighbourhoodName("Sutherland")
                ->setNeighbourhoodId("O48")
                ->setAddress($address);

            $obMan->persist($this->property);
            $obMan->flush();


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
            $this->property = (new Property())
                ->setSiteId(6661489)
                ->setPropertyName("Charlton Legs")
                ->setPropertyType("House")
                ->setPropertyStatus("Active")
                ->setStructureId(54586)
                ->setNumUnits(10)
                ->setNeighbourhoodName("Sutherland")
                ->setNeighbourhoodId("O48")
                ->setAddress($address);

            $obMan->persist($this->property);
            $obMan->flush();
        }
        else
        {
            $obMan->persist($this->property);
            $obMan->flush();
        }
    }
}