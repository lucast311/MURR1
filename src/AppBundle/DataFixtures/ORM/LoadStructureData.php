<?php
namespace AppBundle\DataFixtures\ORM;

use AppBundle\Entity\Property;
use AppBundle\Entity\Address;
use AppBundle\DataFixtures\ORM\LoadAddressData;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class LoadStructureData implements FixtureInterface
{
    public $structure;

    /**
     *
     * @param mixed $structure the property entity passed in
     */
    public function __construct($structure = null)
    {
        // set the structure attribute
        $this->structure = $structure;
    }

    /**
     *
     * @param ObjectManager $obMan the object manager
     */
    public function load(ObjectManager $obMan)
    {
        if(is_null($this->structure))
        {}
        else
        {
            $obMan->persist($this->structure);
            $obMan->flush();
        }
    }
}