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
     * A fixture method to create Contacts in the database for testing
     * @param ObjectManager $obMan the object manager
     */
    public function load(ObjectManager $obMan)
    {
    }
}