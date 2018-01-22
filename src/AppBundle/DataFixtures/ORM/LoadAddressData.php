<?php
namespace AppBundle\DataFixtures\ORM;

use AppBundle\Entity\Contact;
use AppBundle\Entity\Address;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class LoadAddressData implements FixtureInterface
{
    // private attribute that is the address to add
    private $address;

    /**
     * A constructor that sets the private attribute the address passed in
     * @param mixed $address the address entity passed in
     */
    public function __construct($address)
    {
        // set the address attribute
        $this->address = $address;
    }

    /**
     * A fixture method to create Addresses in the database for testing
     * @param ObjectManager $obMan the object manager
     */
    public function load(ObjectManager $obMan)
    {
        /*$address = (new Address())
            ->setStreetAddress("12 15th st east")
            ->setPostalCode('S0E1A0')
            ->setCity('Saskatoon')
            ->setProvince('Saskatchewan')
            ->setCountry('Canada');*/

        // persist the address object set in the constructor to the database
        $obMan->persist($this->address);

        // flush the database connection
        $obMan->flush();
    }
}