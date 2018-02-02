<?php
namespace AppBundle\DataFixtures\ORM;

use AppBundle\Entity\Address;
use AppBundle\Entity\Communication;
use AppBundle\Entity\Contact;
use AppBundle\Entity\ContactProperty;
use AppBundle\Entity\Container;
use AppBundle\Entity\Property;

use AppBundle\DataFixtures\ORM\LoadAddressData;
use AppBundle\DataFixtures\ORM\LoadCommunicationData;
use AppBundle\DataFixtures\ORM\LoadContactData;
//use AppBundle\DataFixtures\ORM\LoadContactPropertyData;
use AppBundle\DataFixtures\ORM\LoadContainerData;
use AppBundle\DataFixtures\ORM\LoadPropertyData;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class LoadCommunicationData implements FixtureInterface
{
    // private attribute that is the container to add
    private $communication;

    /**
     * A constructor that sets the private attribute the container passed in
     * @param mixed $container the container entity passed in
     */
    public function __construct($communication = null)
    {
        // set the container attribute
        $this->communication = $communication;
    }

    /**
     * A fixture method to create Containers in the database for testing
     * @param ObjectManager $obMan the object manager
     */
    public function load(ObjectManager $obMan)
    {
        if(is_null($this->communication))
        {
            //custom, independant autoloaded fixtures

            //////////////////// Loading a property

            // create a single address
            $address = (new Address())
                ->setStreetAddress("123 Fake St")
                ->setPostalCode("A1A 1A1")
                ->setCity("Saskatoon")
                ->setProvince("Saskatchewan")
                ->setCountry("Canada");

            $addressFixtureLoader = new LoadAddressData($address);
            $addressFixtureLoader->load($obMan);


            // create a single property
            $property = (new Property())
                ->setSiteId(555)
                ->setPropertyName("Cosmo")
                ->setPropertyType("Townhouse Condo")
                ->setPropertyStatus("Active")
                ->setStructureId(94)
                ->setNumUnits(1)
                ->setNeighbourhoodName("Sutherland")
                ->setNeighbourhoodId("O48")
                ->setAddress($address);

            $propertyFixtureLoader = new LoadPropertyData($property);
            $propertyFixtureLoader->load($obMan);

            ////////////////////////////////


            // create a communication to search for in the test
            $this->communication = (new Communication())
                ->setDate("2018-01-01")
                ->setType("Phone")
                ->setMedium("Incoming")
                ->setCategory("Collection")
                ->setDescription("Its a bin")
                ->setProperty($property);

            // persist the container object set in the constructor to the database
            $obMan->persist($this->communication);
            // flush the database connection
            $obMan->flush();


        }
        else
        {
            // persist the container object set in the constructor to the database
            $obMan->persist($this->communication);
            // flush the database connection
            $obMan->flush();
        }

    }
}



?>