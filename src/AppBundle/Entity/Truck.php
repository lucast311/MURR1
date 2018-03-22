<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

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
     * @ORM\Column(name="truckId", type="string", length=6, unique=true)
     * @Assert\Length(min=1,max=6,
     *  minMessage = "Truck ID must be atleast {{ limit }} digits long",
     *  maxMessage = "Truck ID can not be more than {{ limit }} digits long")
     * @Assert\Regex(
     *  pattern="/\d/",
     *  htmlPattern=".*",
     *  message="Truck ID must only contain digits")
     * @Assert\NotNull(message="Please specify a Truck ID.")
     *
     */
    private $truckId;

    /**
     * @var string
     *
     * @ORM\Column(name="type", type="string", length=15)
     * @Assert\Length( min = 1, max = 15,
     *  minMessage = "The Truck Type must be atleast {{ limit }} characters long",
     *  maxMessage = "The Truck Type can not be more than {{ limit }} characters long")
     * @Assert\NotNull(message="Please specify a Type.")
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

