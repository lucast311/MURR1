<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Property
 *
 * @ORM\Table(name="property")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\PropertyRepository")
 */
class Property
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="propertyName", type="string", length=100, nullable=true)
     */
    private $propertyName;

    /**
     * @var string
     *
     * @ORM\Column(name="propertyType", type="string", length=50, nullable=true)
     */
    private $propertyType;

    /**
     * @var string
     *
     * @ORM\Column(name="propertyStatus", type="string", length=50)
     */
    private $propertyStatus;

    /**
     * @var int
     *
     * @ORM\Column(name="structureId", type="integer", nullable=true)
     */
    private $structureId;

    /**
     * @var int
     *
     * @ORM\Column(name="numUnits", type="integer")
     */
    private $numUnits;


    /**
     * @var string
     *
     * @ORM\Column(name="neighbourhoodName", type="string", length=100)
     */
    private $neighbourhoodName;

    /**
     * @var string
     *
     * @ORM\Column(name="neighbourhoodId", type="string", length=25, nullable=true)
     */
    private $neighbourhoodId;

    /**
     * @var \stdClass
     *
     * @ORM\ManyToOne(targetEntity="Address")
     * @ORM\JoinColumn(name="addressId", referencedColumnName="id")
     *
     * @Assert\Valid()
     *
     */
    private $address;

    private $contacts;
    private $bins;
    private $buildings;

    /**
     * Set site id
     *
     * @param int $siteId
     *
     * @return Property
     */
    public function setId($siteId)
    {
        $this->id = $siteId;

        return $this;
    }

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
     * Set propertyName
     *
     * @param string $propertyName
     *
     * @return Property
     */
    public function setPropertyName($propertyName)
    {
        $this->propertyName = $propertyName;

        return $this;
    }

    /**
     * Get propertyName
     *
     * @return string
     */
    public function getPropertyName()
    {
        return $this->propertyName;
    }

    /**
     * Set propertyType
     *
     * @param string $propertyType
     *
     * @return Property
     */
    public function setPropertyType($propertyType)
    {
        $this->propertyType = $propertyType;

        return $this;
    }

    /**
     * Get propertyType
     *
     * @return string
     */
    public function getPropertyType()
    {
        return $this->propertyType;
    }

    /**
     * Set propertyStatus
     *
     * @param string $propertyStatus
     *
     * @return Property
     */
    public function setPropertyStatus($propertyStatus)
    {
        $this->propertyStatus = $propertyStatus;

        return $this;
    }

    /**
     * Get propertyStatus
     *
     * @return string
     */
    public function getPropertyStatus()
    {
        return $this->propertyStatus;
    }

    /**
     * Set structureId
     *
     * @param int $structureId
     *
     * @return Property
     */
    public function setStructureId($structureId)
    {
        $this->structureId = $structureId;

        return $this;
    }

    /**
     * Get structureId
     *
     * @return int
     */
    public function getStructureId()
    {
        return $this->structureId;
    }

    /**
     * Set numUnits
     *
     * @param int $numUnits
     *
     * @return Property
     */
    public function setNumUnits($numUnits)
    {
        $this->numUnits = $numUnits;

        return $this;
    }

    /**
     * Get numUnits
     *
     * @return int
     */
    public function getNumUnits()
    {
        return $this->numUnits;
    }

    /**
     * Set neighbourhoodName
     *
     * @param string $neighbourhoodName
     *
     * @return Property
     */
    public function setNeighbourhoodName($neighbourhoodName)
    {
        $this->neighbourhoodName = $neighbourhoodName;

        return $this;
    }

    /**
     * Get neighbourhoodName
     *
     * @return string
     */
    public function getNeighbourhoodName()
    {
        return $this->neighbourhoodName;
    }

    /**
     * Set neighbourhoodId
     *
     * @param string $neighbourhoodId
     *
     * @return Property
     */
    public function setNeighbourhoodId($neighbourhoodId)
    {
        $this->neighbourhoodId = $neighbourhoodId;

        return $this;
    }

    /**
     * Get neighbourhoodId
     *
     * @return string
     */
    public function getNeighbourhoodId()
    {
        return $this->neighbourhoodId;
    }

    /**
     * Set address
     *
     * @param Address $address
     *
     * @return Property
     */
    public function setAddress($address)
    {
        $this->address = $address;

        return $this;
    }

    /**
     * Get address
     *
     * @return Address
     */
    public function getAddress()
    {
        return $this->address;
    }

    public function getContacts(){}

    public function setContacts($contacts){}

    public function getBins(){}

    public function setBins($bins){}

    public function getBuildings(){}

    public function setBuildings($buildings){}

    public static function getStatuses(){}

    public static function getTypes(){}
}

