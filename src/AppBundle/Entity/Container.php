<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

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
     * @ORM\Column(name="PickUpInfo", type="integer", nullable=true)
     */
    private $pickUpInfo;

    /**
     * @var string
     *
     * @ORM\Column(name="ContainerSerial", type="string", length=50)
     */
    private $containerSerial;

    /**
     * @var string
     *
     * @ORM\Column(name="LocationDesc", type="string", length=255, nullable=true)
     */
    private $locationDesc;

    /**
     * @var string
     *
     * @ORM\Column(name="Long", type="string", length=100, nullable=true)
     */
    private $long;

    /**
     * @var string
     *
     * @ORM\Column(name="lat", type="string", length=100, nullable=true)
     */
    private $lat;

    /**
     * @var string
     *
     * @ORM\Column(name="type", type="string", length=50)
     */
    private $type;

    /**
     * @var string
     *
     * @ORM\Column(name="size", type="string", length=100)
     */
    private $size;

    /**
     * @var bool
     *
     * @ORM\Column(name="isInaccessable", type="boolean", nullable=true)
     */
    private $isInaccessable;

    /**
     * @var string
     *
     * @ORM\Column(name="reasonForInaccassability", type="string", length=255, nullable=true)
     */
    private $reasonForInaccassability;

    /**
     * @var bool
     *
     * @ORM\Column(name="isContaminated", type="boolean", nullable=true)
     */
    private $isContaminated;

    /**
     * @var bool
     *
     * @ORM\Column(name="isGraffiti", type="boolean", nullable=true)
     */
    private $isGraffiti;


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
     * Set pickUpInfo
     *
     * @param integer $pickUpInfo
     *
     * @return Container
     */
    public function setPickUpInfo($pickUpInfo)
    {
        $this->pickUpInfo = $pickUpInfo;

        return $this;
    }

    /**
     * Get pickUpInfo
     *
     * @return int
     */
    public function getPickUpInfo()
    {
        return $this->pickUpInfo;
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

    /**
     * Set isInaccessable
     *
     * @param boolean $isInaccessable
     *
     * @return Container
     */
    public function setIsInaccessable($isInaccessable)
    {
        $this->isInaccessable = $isInaccessable;

        return $this;
    }

    /**
     * Get isInaccessable
     *
     * @return bool
     */
    public function getIsInaccessable()
    {
        return $this->isInaccessable;
    }

    /**
     * Set reasonForInaccassability
     *
     * @param string $reasonForInaccassability
     *
     * @return Container
     */
    public function setReasonForInaccassability($reasonForInaccassability)
    {
        $this->reasonForInaccassability = $reasonForInaccassability;

        return $this;
    }

    /**
     * Get reasonForInaccassability
     *
     * @return string
     */
    public function getReasonForInaccassability()
    {
        return $this->reasonForInaccassability;
    }

    /**
     * Set isContaminated
     *
     * @param boolean $isContaminated
     *
     * @return Container
     */
    public function setIsContaminated($isContaminated)
    {
        $this->isContaminated = $isContaminated;

        return $this;
    }

    /**
     * Get isContaminated
     *
     * @return bool
     */
    public function getIsContaminated()
    {
        return $this->isContaminated;
    }

    /**
     * Set isGraffiti
     *
     * @param boolean $isGraffiti
     *
     * @return Container
     */
    public function setIsGraffiti($isGraffiti)
    {
        $this->isGraffiti = $isGraffiti;

        return $this;
    }

    /**
     * Get isGraffiti
     *
     * @return bool
     */
    public function getIsGraffiti()
    {
        return $this->isGraffiti;
    }
}

