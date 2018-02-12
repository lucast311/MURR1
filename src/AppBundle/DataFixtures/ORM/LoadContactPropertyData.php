<?php
namespace AppBundle\DataFixtures\ORM;

use AppBundle\Entity\Contact;
use AppBundle\Entity\Property;
use AppBundle\Entity\ContactProperty;
use AppBundle\DataFixtures\ORM\LoadAddressData;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class LoadContactPropertyData implements FixtureInterface
{
    // private attribute that is the container to add
    private $container;

    /**
     * A constructor that sets the private attribute the container passed in
     * @param mixed $container the container entity passed in
     */
    public function __construct($container = null)
    {
        // set the container attribute
        $this->container = $container;
    }

    /**
     * A fixture method to create Containers in the database for testing
     * @param ObjectManager $obMan the object manager
     */
    public function load(ObjectManager $obMan)
    {
        if(is_null($this->container))
        {
            //custom, independant autoloaded fixtures
        }

        //// persist the container object set in the constructor to the database
        //$obMan->persist($this->container);
        //// flush the database connection
        //$obMan->flush();
    }
}

?>