<?php
namespace AppBundle\DataFixtures\ORM;

use AppBundle\Entity\Container;
use AppBundle\Entity\Property;
use AppBundle\Entity\Address;
use AppBundle\Entity\Structure;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class LoadContainerData implements FixtureInterface
{
    // private attribute that is the container to add
    private $container;

    /**
     * A constructor that sets the private attribute the container passed in
     * @param mixed $container the container entity passed in
     */
    public function __construct($container = null)
    {
        // set the container attribute
        $this->container = $container;
    }

    /**
     * A fixture method to create Containers in the database for testing
     * @param ObjectManager $obMan the object manager
     */
    public function load(ObjectManager $obMan)
    {
        if(is_null($this->container))
        {
            //Address data
            $address = (new Address())
                ->setStreetAddress("Test ST")
                ->setPostalCode('T3S 3T4')
                ->setCity('Saskatoon')
                ->setProvince('Saskatchetest')
                ->setCountry('Testnada');

            $address2 = (new Address())
                ->setStreetAddress("Ack Street")
                ->setPostalCode('R3E 3E3')
                ->setCity('Regina')
                ->setProvince('Ontario')
                ->setCountry('USA');

            $addressFixtureLoader = new LoadAddressData($address);
            $addressFixtureLoader = new LoadAddressData($address2);

            $addressFixtureLoader->load($obMan);

            // Property data
            $property = (new Property())
                ->setSiteId((2363566))
                ->setPropertyName("Cosmo")
                ->setPropertyType("Townhouse Condo")
                ->setPropertyStatus("Active")
                ->setStructureId(54586)
                ->setNumUnits(5)
                ->setNeighbourhoodName("Sutherland")
                ->setNeighbourhoodId("O48")
                ->setAddress($address);

            $property2 = (new Property())
                ->setSiteId((2363777))
                ->setPropertyName("NewTestProp")
                ->setPropertyType("Townhouse Condo")
                ->setPropertyStatus("Active")
                ->setStructureId(545677)
                ->setNumUnits(14)
                ->setNeighbourhoodName("Evergreen")
                ->setNeighbourhoodId("O49")
                ->setAddress($address2);

            $PropertyFixtureLoader = new LoadPropertyData($property);
            $PropertyFixtureLoader = new LoadPropertyData($property2);

            $PropertyFixtureLoader->load($obMan);

            // Structure data
            $structure = (new Structure())
                ->setProperty(143546)
                ->setDescription("Hello World");

            $structureFixtureLoader = new LoadStructureData($structure);

            $structureFixtureLoader->load($obMan);

            //custom, independant autoloaded fixtures
            $this->container = (new Container())
                ->setFrequency("Weekly")
                ->setContainerSerial("123457")
                ->setLocationDesc("South-west side")
                ->setLon("87")
                ->setLat("88")
                ->setType("Cart")
                ->setSize("6 yd")
                ->setAugmentation("Wheels")
                ->setStatus("Active")
                ->setReasonForStatus("Everything normal")
                ->setProperty($property)
                ->setStructure($structure);



            $obMan->persist($this->container);

            $obMan->flush();

            $this->container = (new Container())
                ->setFrequency("Weekly")
                ->setContainerSerial("888888")
                ->setLocationDesc("North-East side")
                ->setLon("51")
                ->setLat("56")
                ->setType("Bin")
                ->setSize("12 yd")
                ->setAugmentation("Locks")
                ->setStatus("Active")
                ->setReasonForStatus("Everything normal")
                ->setProperty($property2)
                ->setStructure($structure);

            $obMan->persist($this->container);

            $obMan->flush();

            for ($i = 1; $i <= 10; $i++)
            {
                sleep(1);

            	$this->container = (new Container())
                    ->setFrequency("Weekly")
                    ->setContainerSerial("QWERTY" . $i)
                    ->setLocationDesc("South-west side")
                    ->setLon(87)
                    ->setLat(88)
                    ->setType("Cart")
                    ->setSize("6 yd")
                    ->setAugmentation("Wheels")
                    ->setStatus("Active")
                    ->setReasonForStatus("Everything normal")
                    ->setProperty($property);

                $obMan->persist($this->container);

                $obMan->flush();
            }

            sleep(1);

        }
        else
        {
            // persist the container object set in the constructor to the database
            $obMan->persist($this->container);
            // flush the database connection
            $obMan->flush();
        }
    }
}