<?php
namespace AppBundle\DataFixtures\ORM;

use AppBundle\Entity\User;
use AppBundle\DataFixtures\ORM\LoadAddressData;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class LoadUserData implements FixtureInterface
{
    /**
     * Story_15a
     * @param ObjectManager $obMan the object manager
     */
    public function load(ObjectManager $obMan){}
}