<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Container
 *
 * @ORM\Table(name="container")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\ContainerRepository")
 */
class Container
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
     * @var int
     *
     * @ORM\Column(name="PickUpInfo", type="string", nullable=true)
     * @Assert\Choice(callback="getFrequencyChoices", message = "Please select frequency type")
     */
    private $frequency;

    /**
     * @var string
     *
     * @ORM\Column(name="ContainerSerial", type="string", length=50, unique=true)
     */
    private $containerSerial;

    

    /**
     * @var string
     *
     * @ORM\Column(name="type", type="string", length=50)
     * @Assert\Choice(callback="getTypeOptions", message = "Please select bin Type")
     */
    private $type;

    /**
     * @var string
     *
     * @ORM\Column(name="size", type="string", length=100)
     */
    private $size;

    /**
     * @var string
     * @ORM\Column(name="status", type="string", length=50)
     * @Assert\Choice(callback="getStatusChoices", message = "Please select bin status")
     */
    private $status;

    ///**
    // * @var bool
    // *
    // * @ORM\Column(name="isInaccessable", type="boolean", nullable=true)
    // */
    //private $isInaccessable;

    

    /**
     * @ORM\ManyToOne(targetEntity="Property", inversedBy="bins", cascade={"persist"})
     * @ORM\JoinColumn(name="propertyID", referencedColumnName="id")
     */
    protected $property;

    ///**
    // * @var bool
    // *
    // * @ORM\Column(name="isContaminated", type="boolean", nullable=true)
    // */
    //private $isContaminated;

    ///**
    // * @var bool
    // *
    // * @ORM\Column(name="isGraffiti", type="boolean", nullable=true)
    // */
    //private $isGraffiti;


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
     * Set containerSerial
     *
     * @param string $containerSerial
     *
     * @return Container
     */
    public function setContainerSerial($containerSerial)
    {
        $this->containerSerial = $containerSerial;

        return $this;
    }

    /**
     * Get containerSerial
     *
     * @return string
     */
    public function getContainerSerial()
    {
        return $this->containerSerial;
    }



    /**
     * Set type
     *
     * @param string $type
     *
     * @return Container
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

    /**
     * Set size
     *
     * @param string $size
     *
     * @return Container
     */
    public function setSize($size)
    {
        $this->size = $size;

        return $this;
    }

    /**
     * Get size
     *
     * @return string
     */
    public function getSize()
    {
        return $this->size;
    }


    public function setFrequency($frequency)
    {
        $this->frequency = $frequency;
        return $this;
    }

    public function getFrequency()
    {
        return $this->frequency;
    }

    public function setStatus($status)
    {
        $this->status = $status;
        return $this;
    }

    public function getStatus()
    {
        return $this->status;
    }


    /**
     * Gets the choices available for the Type attribute
     *
     * @return array
     */

    public static function getTypeChoices()
    {
        return array('Bin' => 'Bin',
                     'Recycling Bin' => 'Recycling Bin',
                     'Garbage Bin' => 'Garbage Bin');
    }

    public static function getStatusChoices()
    {
        return array('Active' => 'Active',
                     'Inaccessable' => 'Inaccessable',
                     'Contaminated' => 'Contaminated',
                     'Damage' => 'Damage',
                     'Graffiti' => 'Graffiti');
    }

    public static function getFrequencyChoices()
    {
        return array('Monthly' => 'Monthly',
                     'Weekly' => 'Weekly',
                     'Bi-weekly' => 'Bi-weekly',
                     'Bi-monthly' => 'Bi-monthly');
    }
}

