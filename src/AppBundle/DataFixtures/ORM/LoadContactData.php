<?php
namespace AppBundle\DataFixtures\ORM;

use AppBundle\Entity\Contact;
use AppBundle\Entity\Address;
use AppBundle\DataFixtures\ORM\LoadAddressData;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

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