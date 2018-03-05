<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Truck
 *
 * @ORM\Table(name="truck")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\TruckRepository")
 */
class Truck
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="truckId", type="string", length=6)
     */
    private $truckId;

    /**
     * @var string
     *
     * @ORM\Column(name="type", type="string", length=15)
     */
    private $type;


    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set truckId
     *
     * @param string $truckId
     *
     * @return Truck
     */
    public function setTruckId($truckId)
    {
        $this->truckId = $truckId;

        return $this;
    }

    /**
     * Get truckId
     *
     * @return string
     */
    public function getTruckId()
    {
        return $this->truckId;
    }

    /**
     * Set type
     *
     * @param string $type
     *
     * @return Truck
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }
}

