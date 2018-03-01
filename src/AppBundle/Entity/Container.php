<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * Container
 *
 * @ORM\Table(name="container")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\ContainerRepository")
 * @UniqueEntity(fields = {"containerSerial"}, message = "Serial already exists")
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
     * @ORM\Column(name="frequency", type="string", nullable=true)
     * @Assert\Choice(callback="FrequencyChoices", message = "Please select frequency type")
     */
    private $frequency;

    /**
     * @var string
     *
     * @ORM\Column(name="ContainerSerial", type="string", length=50, unique=true)
     * @Assert\Length(max=50, maxMessage="Length cannot be more than 50 characters")
     * @Assert\NotBlank()
     * @Assert\NotNull()
     */
    private $containerSerial;

    /**
     * @var string
     *
     * @ORM\Column(name="LocationDesc", type="string", length=255, nullable=true)
     * @Assert\Length(max=255, maxMessage="Length cannot be more than 255 characters")
     */
    private $locationDesc;

    /**
     * @var float
     *
     * @ORM\Column(name="Lon", type="float", length=100, nullable=true)
     * @Assert\Range(min=-180,max=180,minMessage="Longitude must be higher than or equal to -180", maxMessage="Longitude must be lower than or equal to 180")
     */
    private $lon;

    /**
     * @var float
     *
     * @ORM\Column(name="lat", type="string", nullable=true)
     * @Assert\Range(min=-90,max=90,minMessage="Latitude must be more than or equal to -90", maxMessage="Lattitude must be lower than or equal to 90")
     */
    private $lat;



    /**
     * @var string
     *
     * @ORM\Column(name="type", type="string", length=50)
     * @Assert\Choice(callback="TypeChoices", message = "Please select bin Type")
     * @Assert\NotNull()
     */
    private $type;

    /**
     * @var string
     *
     * @ORM\Column(name="size", type="string", length=100)
     * @Assert\Length(max=100, maxMessage="Size can only be 100 characters")
      *@Assert\NotNull(message="Please indicate a size for the container")
     */
    private $size;

    /**
     * @var string
     * @ORM\Column(name="status", type="string", length=50)
     * @Assert\Choice(callback="StatusChoices", message = "Please select bin status")
     */
    private $status;


    /**
     * @var string
     *
     * @ORM\Column(name="reasonForStatus", type="string", length=255, nullable=true)
     * @Assert\Length(max="255", maxMessage="Can only be 255 characters")
     */
    private $reasonForStatus;




    /**
     * @ORM\ManyToOne(targetEntity="Property", inversedBy="bins", cascade={"persist"})
     * @ORM\JoinColumn(name="propertyID", referencedColumnName="id")
     */
    protected $property;


    /**
     *@ORM\Column(name="augmentation", type="string", length=255, nullable=true)
     *@Assert\Length(max=255, maxMessage="Size must be less than 255")
     *
     * @var string
     */
    private $augmentation;

    /**
     * @ORM\ManyToOne(targetEntity="Structure", cascade={"persist"})
     * @ORM\JoinColumn(name="structureId", referencedColumnName="id")
     */
    private $structure;

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
     * Set locationDesc
     *
     * @param string $locationDesc
     *
     * @return Container
     */
    public function setLocationDesc($locationDesc)
    {
        $this->locationDesc = $locationDesc;

        return $this;
    }

    /**
     * Get locationDesc
     *
     * @return string
     */
    public function getLocationDesc()
    {
        return $this->locationDesc;
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
     * Set long
     *
     * @param string $long
     *
     * @return Container
     */
    public function setLong($long)
    {
        $this->long = $long;

        return $this;
    }

    /**
     * Get type
     *
     * @return string
     */
    public function getProperty()
    {
        return $this->property;
    }

    /**
     * Set long
     *
     * @param string $long
     *
     * @return Container
     */
    public function setProperty($property)
    {
        $this->property = $property;

        return $this;
    }

    /**
     * Get long
     *
     * @return string
     */
    public function getLong()
    {
        return $this->long;
    }

    /**
     * Set lat
     *
     * @param string $lat
     *
     * @return Container
     */
    public function setLat($lat)
    {
        $this->lat = $lat;

        return $this;
    }

    /**
     * Get lat
     *
     * @return string
     */
    public function getLat()
    {
        return $this->lat;
    }

    public function setReasonForStatus($reasonForStatus)
    {
        $this->reasonForStatus = $reasonForStatus;
        return $this;
    }

    public function getReasonForStatus()
    {
        return $this->reasonForStatus;
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


    public function setAugmentation($augmentation)
    {
        $this->augmentation = $augmentation;
        return $this;
    }

    public function getAugmentation()
    {
        return $this->augmentation;
    }

    public function setStructure($structure)
    {
        $this->structure = $structure;
        return $this;
    }

    public function getStructure()
    {
        return $this->structure;
    }

    /**
     * Gets the choices available for the Type attribute
     *
     * @return array
     */
    public static function TypeChoices()
    {
        return array('Bin' => 'Bin',
                     'Cart'=>'Cart');
    }

    /**
     * Get the choices for the Status in the its dropdown
     * @return string[]
     */
    public static function StatusChoices()
    {
        return array('Active' => 'Active',
                     'Inaccessable' => 'Inaccessable',
                     'Contaminated' => 'Contaminated',
                     'Damage' => 'Damage',
                     'Graffiti' => 'Graffiti');
    }

    /**
     *  Get the choices for the Frequency in the its dropdown
     * @return string[]
     */
    public static function FrequencyChoices()
    {
        return array('Monthly' => 'Monthly',
                     'Weekly' => 'Weekly',
                     'Twice weekly' => 'Twice weekly');
    }

    public function __toString()
    {
        return $this->containerSerial; 
    }
}

