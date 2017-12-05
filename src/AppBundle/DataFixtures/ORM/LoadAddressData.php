<?php
namespace AppBundle\DataFixtures\ORM;

use AppBundle\Entity\Contact;
use AppBundle\Entity\Address;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class LoadAddressData implements FixtureInterface
{
    private $address;
    public function __construct($address)
    {
        $this->address = $address;
    }

    public function load(ObjectManager $obMan)
    {
        /*$address = (new Address())
            ->setStreetAddress("12 15th st east")
            ->setPostalCode('S0E1A0')
            ->setCity('Saskatoon')
            ->setProvince('Saskatchewan')
            ->setCountry('Canada');*/
        $obMan->persist($this->address);
        $obMan->flush();
    }
}