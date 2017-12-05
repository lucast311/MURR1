<?php
namespace AppBundle\DataFixtures\ORM;

use AppBundle\Entity\Contact;
use AppBundle\Entity\Address;
use AppBundle\DataFixtures\ORM\LoadAddressData;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class LoadContactData implements FixtureInterface
{
    public function load(ObjectManager $obMan)
    {
        for($i=0;$i<100;$i++)
        {
            $contact = (new Contact())
                ->setFirstName("Testy".uniqid())
                ->setLastName("McTest")
                ->setOrganization("TestCorp LLC.")
                ->setPrimaryPhone("969-555-6969")
                ->setPhoneExtention(696)
                ->setEmailAddress("tmctest@testcorp.com");

            $obMan->persist($contact);
            $obMan->flush();
        }

        for($i=0;$i<50;$i++)
        {
            $address = (new Address())
                ->setStreetAddress("12 15th st east")
                ->setPostalCode('S0E1A0')
                ->setCity('Saskatoon')
                ->setProvince('Saskatchewan')
                ->setCountry('Canada');
            $addressFixtureLoader = new LoadAddressData($address);
            $addressFixtureLoader->load($obMan);

            $contact = (new Contact())
                ->setFirstName("Bob")
                ->setLastName("Jones")
                ->setPrimaryPhone("969-555-6969")
                ->setPhoneExtention(123)
                ->setEmailAddress("I@L.com")
                ->setAddress($address);

            $obMan->persist($contact);
            $obMan->flush();
        }

        $address = (new Address())
            ->setStreetAddress("123 Main Street")
            ->setPostalCode('S7N 0R7')
            ->setCity('Saskatoon')
            ->setProvince('Saskatchewan')
            ->setCountry('Canada');
        $addressFixtureLoader = new LoadAddressData($address);
        $addressFixtureLoader->load($obMan);

        $contact = (new Contact())
            ->setFirstName("Bob")
            ->setLastName("Frank")
            ->setOrganization('Murr')
            ->setPrimaryPhone("306-921-3344")
            ->setPhoneExtention(123)
            ->setEmailAddress("murr123@gmail.com");

        $obMan->persist($contact);
        $obMan->flush();

        $contact = (new Contact())
            ->setFirstName("Jim")
            ->setLastName("Jim")
            ->setPrimaryPhone("969-555-6969")
            ->setPhoneExtention(123)
            ->setEmailAddress("tmctest@testcorp.com");

        $obMan->persist($contact);
        $obMan->flush();
    }
}