<?php
namespace AppBundle\DataFixtures\ORM;

use AppBundle\Entity\Truck;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class LoadTruckData implements FixtureInterface
{
    // private attribute that is the truck to (explicitly) add
    private $truck;

    /**
     * A constructor that sets the private attribute to the truck passed in
     * @param mixed $truck the truck entity passed in
     */
    public function __construct($truck = null)
    {
        // set the truck attribute
        $this->truck = $truck;
    }

    /**
     * A fixture method to create Trucks in the database for testing
     * @param ObjectManager $obMan the object manager
     */
    public function load(ObjectManager $obMan)
    {
        //if a truck wasnt explicitly defined, these will be loaded
        if(is_null($this->truck))
        {
            //add 5 Large
            for($i=0;$i<5;$i++){
                $id = "0000";
                $id.=($i+30);
                $this->truck = (new Truck())
                    ->setTruckId($id)
                    ->setType("Large");

                $obMan->persist($this->truck);
                $obMan->flush();
            }

            //add 5 Medium
            for($i=0;$i<5;$i++){
                $id = "0000";
                $id.=($i+20);
                $this->truck = (new Truck())
                    ->setTruckId($id)
                    ->setType("Medium");

                $obMan->persist($this->truck);
                $obMan->flush();
            }

            //add 5 Small
            for($i=0;$i<5;$i++){
                $id = "0000";
                $id.=($i+10);
                $this->truck = (new Truck())
                    ->setTruckId($id)
                    ->setType("Small");

                $obMan->persist($this->truck);
                $obMan->flush();
            }
        }
        else
        {
            // persist the truck object set in the constructor to the database
            $obMan->persist($this->truck);
            // flush the database connection
            $obMan->flush();
        }
    }
}