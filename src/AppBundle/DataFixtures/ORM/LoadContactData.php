<?php
namespace AppBundle\DataFixtures\ORM;

use AppBundle\Entity\Contact;
use AppBundle\Entity\Address;
use AppBundle\Entity\Property;
use AppBundle\DataFixtures\ORM\LoadAddressData;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

use Doctrine\Common\Collections\ArrayCollection;

class LoadContactData implements FixtureInterface
{
    // private attribute that is the Contact to add
    private $contact;

    /**
     * A constructor that sets the private attribute the Contact passed in
     * @param mixed $container the Contact entity passed in
     */
    public function __construct($contact = null)
    {
        // set the Contact attribute
        $this->contact = $contact;
    }

    /**
     * A fixture method to create Contacts in the database for testing
     * @param ObjectManager $obMan the object manager
     */
    public function load(ObjectManager $obMan)
    {
        if(is_null($this->contact))
        {
            // create 100 Contacts with the following data
            for($i=0;$i<10;$i++)
            {
                // Address data
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

                // Contact data
                $this->contact = (new Contact())
                    ->setFirstName(uniqid()."Testy")
                    ->setLastName("McTest")
                    ->setRole("Property Manager")
                    ->setCompanyName("TestCorp LLC.")
                    ->setPrimaryPhone("969-555-6969")
                    ->setPhoneExtension(696)
                    ->setEmailAddress("tmctest@testcorp.com")
                    ->setAddress($address);


                // add the Contact to the database
                $obMan->persist($this->contact);

                // flush the database connection
                $obMan->flush();
            }

            // create 50 Contacts with the following data
            for($i=0;$i<10;$i++)
            {
                $address = (new Address())
                    ->setStreetAddress("12 15th st east")
                    ->setPostalCode('S0E1A0')
                    ->setCity('Saskatoon')
                    ->setProvince('Saskatchewan')
                    ->setCountry('Canada');

                $addressFixtureLoader = new LoadAddressData($address);
                $addressFixtureLoader->load($obMan);

                $this->contact = (new Contact())
                    ->setFirstName("Bob")
                    ->setLastName("Jones")
                    ->setRole("Property Manager")
                    ->setPrimaryPhone("969-555-6969")
                    ->setPhoneExtension(123)
                    ->setEmailAddress("I@L.com")
                    ->setAddress($address);

                $obMan->persist($this->contact);
                $obMan->flush();
            }

            // create a single address
            $address = (new Address())
                ->setStreetAddress("123 Main Street")
                ->setPostalCode('S7N 0R7')
                ->setCity('Saskatoon')
                ->setProvince('Saskatchewan')
                ->setCountry('Canada');

            $addressFixtureLoader = new LoadAddressData($address);
            $addressFixtureLoader->load($obMan);

            // create a single contact
            $this->contact = (new Contact())
                ->setFirstName("Bob")
                ->setLastName("Frank")
                ->setRole("Property Manager")
                ->setCompanyName('Murr')
                ->setsecondaryPhone("911-911-9111")
                ->setPrimaryPhone("306-921-3344")
                ->setPhoneExtension(123)
                ->setEmailAddress("murr123@gmail.com");

            $obMan->persist($this->contact);
            $obMan->flush();

            // create a single contact
            $this->contact = (new Contact())
                ->setFirstName("Jim")
                ->setLastName("Jim")
                ->setPrimaryPhone("969-555-6969")
                ->setRole("Property Manager")
                ->setPhoneExtension(123)
                ->setEmailAddress("tmctest@testcorp.com");

            $obMan->persist($this->contact);
            $obMan->flush();


            // story 4k
            $this->contact = (new Contact())
                ->setFirstName("Bill")
                ->setLastName("Smith")
                ->setPrimaryPhone("123-321-6439")
                ->setRole("Property Manager")
                ->setPhoneExtension(123)
                ->setEmailAddress("billsmith@email.com");

            $obMan->persist($this->contact);
            $obMan->flush();
            //create two properties to be already associated for this contact
            $property = (new Property())
               ->setSiteId(333666999)
               ->setPropertyName("Balla Highrize")
               ->setNumUnits(102)
               ->setPropertyStatus("Active")
               ->setPropertyType("High Rise Apartment")
               ->setNeighbourhoodName("Compton")
               ->setAddress((new Address())
                   ->setStreetAddress("456 West Street")
                   ->setCity("Compton")
                   ->setCountry("America")
                   ->setPostalCode("A1A 1A1")
                   ->setProvince("CA"));

            $property2 = (new Property())
               ->setSiteId(999666333)
               ->setPropertyName("Thug Muny Apts.")
               ->setNumUnits(102)
               ->setPropertyStatus("Active")
               ->setPropertyType("High Rise Apartment")
               ->setNeighbourhoodName("Compton")
               ->setAddress((new Address())
                   ->setStreetAddress("726 East Street")
                   ->setCity("Compton")
                   ->setCountry("America")
                   ->setPostalCode("A1A 1A1")
                   ->setProvince("CA"));

            $property3 = (new Property())
                ->setSiteId(666333999)
                ->setPropertyName("El Apartamento")
                ->setNumUnits(50)
                ->setPropertyStatus('Active')
                ->setPropertyType('High Rise Apartment')
                ->setNeighbourhoodName('West side')
                ->setAddress((new Address())
                    ->setStreetAddress("1132 Illinois Avenue")
                    ->setCity("Chicago")
                    ->setCountry("America")
                    ->setPostalCode("A1A 1A1")
                    ->setProvince("IL"));

            //create the contact
            $this->contact = (new Contact())
                ->setFirstName("Bill")
                ->setLastName("Jones")
                ->setPrimaryPhone("123-321-6439")
                ->setRole("Property Manager")
                ->setPhoneExtension(321)
                ->setEmailAddress("billjones@webmail.com");
            //set the three properties
            $this->contact->setProperties(new ArrayCollection(array($property,$property2,$property3)));

            //save the contact and properties
            $obMan->persist($this->contact);
            $obMan->flush();




        }
        else
        {
            // persist the contact object set in the constructor to the database
            $obMan->persist($this->contact);
            // flush the database connection
            $obMan->flush();
        }
    }
}