<?php
namespace AppBundle\DataFixtures\ORM;

use AppBundle\Entity\Address;
use AppBundle\Entity\Communication;
use AppBundle\Entity\Contact;
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

use Doctrine\Common\Collections\ArrayCollection;

class LoadCommunicationData implements FixtureInterface
{
    // private attribute that is the communication to add
    private $communication;

    /**
     * A constructor that sets the private attribute the communication passed in
     * @param mixed $communication the communication entity passed in
     */
    public function __construct($communication = null)
    {
        // set the communication attribute
        $this->communication = $communication;
    }

    /**
     * A fixture method to create communication in the database for testing
     * @param ObjectManager $obMan the object manager
     */
    public function load(ObjectManager $obMan)
    {
        if(is_null($this->communication))
        {
            //custom, independant autoloaded fixtures


            // Story 11c - For controller test
            $this->communication = (new Communication())
                ->setDate("2018-01-01")
                ->setType("Phone")
                ->setMedium("Incoming")
                ->setCategory("Multi-purpose")
                ->setDescription("Its a bin");

            $obMan->persist($this->communication);
            $obMan->flush();


            // Story 11c - for most repository tests
            $address = (new Address())
                ->setStreetAddress("123 Main Street")
                ->setPostalCode("S7N 3K5")
                ->setCity("Saskatoon")
                ->setProvince("Saskatchewan")
                ->setCountry("Canada");

            $addressFixtureLoader = new LoadAddressData($address);
            $addressFixtureLoader->load($obMan);

            $property = (new Property())
                ->setSiteId(123)
                ->setPropertyName("123 Fake Street")
                ->setPropertyType("Townhouse Apartment")
                ->setPropertyStatus("Active")
                ->setStructureId(1)
                ->setNumUnits(1)
                ->setNeighbourhoodName("Sutherland")
                ->setNeighbourhoodId("2")
                ->setAddress($address);

            $propertyFixtureLoader = new LoadPropertyData($property);
            $propertyFixtureLoader->load($obMan);

            $this->communication = (new Communication())
                ->setDate("2018-01-01")
                ->setType("Phone")
                ->setMedium("Incoming")
                ->setCategory("Collection")
                ->setDescription("Its a bin")
                ->setContactName("Ken")
                ->setContactEmail("email@email.com")
                ->setContactPhone("111-111-1111")
                ->setProperty($property);

            $obMan->persist($this->communication);
            $obMan->flush();


            // Story 11c - test SearchNarrower works
            $this->communication = (new Communication())
                ->setDate("2018-01-01")
                ->setType("Phone")
                ->setMedium("Incoming")
                ->setCategory("Collection")
                ->setDescription("Its a bin")
                ->setContactName("Steve")
                ->setContactEmail("email@email.com")
                ->setContactPhone("111-111-1111");

            $obMan->persist($this->communication);
            $obMan->flush();


            // Story 11c - test that a communication can be searched
            //  based on a non-communication field it it is associated.
            $address = (new Address())
                ->setStreetAddress("123 Fake St")
                ->setPostalCode("A1A 1A1")
                ->setCity("Saskatoon")
                ->setProvince("Saskatchewan")
                ->setCountry("Canada");

            $addressFixtureLoader = new LoadAddressData($address);
            $addressFixtureLoader->load($obMan);

            $property1 = (new Property())
                ->setSiteId(69696961)
                ->setPropertyName("Cosmo")
                ->setPropertyType("Townhouse Condo")
                ->setPropertyStatus("Active")
                ->setStructureId(94)
                ->setNumUnits(1)
                ->setNeighbourhoodName("Sutherland")
                ->setNeighbourhoodId("O48")
                ->setAddress($address);

            $propertyFixtureLoader = new LoadPropertyData($property1);
            $propertyFixtureLoader->load($obMan);

            $address = (new Address())
                ->setStreetAddress("123 Fake St")
                ->setPostalCode("A1A 1A1")
                ->setCity("Saskatoon")
                ->setProvince("Saskatchewan")
                ->setCountry("Canada");

            $addressFixtureLoader = new LoadAddressData($address);
            $addressFixtureLoader->load($obMan);

            $property2 = (new Property())
                ->setSiteId(69696962)
                ->setPropertyName("SIAST")
                ->setPropertyType("House")
                ->setPropertyStatus("Active")
                ->setStructureId(100)
                ->setNumUnits(20)
                ->setNeighbourhoodName("Test")
                ->setNeighbourhoodId("666")
                ->setAddress($address);

            $propertyFixtureLoader = new LoadPropertyData($property2);
            $propertyFixtureLoader->load($obMan);

            $contact = (new Contact())
                ->setFirstName("Ken")
                ->setLastName("Kenson")
                ->setRole("Property Manager")
                ->setCompanyName("Cosmo")
                ->setPrimaryPhone("222-222-2222")
                ->setPhoneExtension(111)
                ->setEmailAddress("email@email.ca")
                ->setAddress($address);
            $contact->setProperties(new ArrayCollection(array($property1, $property2)));

            $contactFixtureLoader = new LoadContactData($contact);
            $contactFixtureLoader->load($obMan);

            $this->communication = (new Communication())
                ->setDate("Test")
                ->setType("Test")
                ->setMedium("Test")
                ->setCategory("Test")
                ->setDescription("Test")
                ->setProperty($property1);

            $obMan->persist($this->communication);
            $obMan->flush();


            // Story 11c - test that a communication can be searched
            //  based on a forward-slash
            $this->communication = (new Communication())
                ->setDate("Test")
                ->setType("Test")
                ->setMedium("Test")
                ->setCategory("N/A")
                ->setDescription("Test")
                ->setProperty($property1);

            $obMan->persist($this->communication);
            $obMan->flush();


            // Story 11c - test that a communication can be searched
            //  based on a back-slash
            $this->communication = (new Communication())
                ->setDate("Test")
                ->setType("Test")
                ->setMedium("Test")
                ->setCategory("\\")
                ->setDescription("Test")
                ->setProperty($property1);

            $obMan->persist($this->communication);
            $obMan->flush();
        }
        else
        {
            // persist the communication object set in the constructor to the database
            $obMan->persist($this->communication);
            // flush the database connection
            $obMan->flush();
        }
    }
}