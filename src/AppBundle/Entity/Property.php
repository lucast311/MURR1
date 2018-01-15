<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
/**
 * Property
 *
 * @ORM\Table(name="property")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\PropertyRepository")
 *
 * @UniqueEntity(fields = {"siteId"}, message = "Site Id already exists")
 */
class Property
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
     * @ORM\Column(name="siteId", type="integer", unique=true)
     *
     * @Assert\GreaterThan(value = 0, message = "Please specify a valid Site ID")
     * @Assert\NotNull(message = "Please specify a Site ID")
     */
    private $siteId;

    /**
     * @var string
     *
     * @ORM\Column(name="propertyName", type="string", length=100, nullable=true)
     *
     * @Assert\Length(max = 100, maxMessage = "Property name must be less than {{ limit }} characters")
     */
    private $propertyName;

    /**
     * @var string
     *
     * @ORM\Column(name="propertyType", type="string", length=50, nullable=true)
     *
     * @Assert\Length(max = 50, maxMessage = "Property type must be less than {{ limit }} characters")
     * @Assert\Choice(callback = "getTypes", message = "Invalid property type")
     */
    private $propertyType;

    /**
     * @var string
     *
     * @ORM\Column(name="propertyStatus", type="string", length=50)
     *
     * @Assert\NotBlank(message = "Please specify a Property Status")
     * @Assert\Length(max = 50, maxMessage = "Property status must be less than {{ limit }} characters")
     * @Assert\Choice(callback = "getStatuses", message = "Invalid property status")
     */
    private $propertyStatus;

    /**
     * @var int
     *
     * @ORM\Column(name="structureId", type="integer", nullable=true)
     *
     * @Assert\GreaterThan(value = 0, message = "Please specify a valid Structure ID")
     */
    private $structureId;

    /**
     * @var int
     *
     * @ORM\Column(name="numUnits", type="integer")
     *
     * @Assert\NotBlank(message = "Please specify the number of units")
     * @Assert\GreaterThan(value = 0, message = "Please specify a valid number of units")
     *
     */
    private $numUnits;


    /**
     * @var string
     *
     * @ORM\Column(name="neighbourhoodName", type="string", length=100)
     *
     * @Assert\Length(max = 100, maxMessage = "Neighbourhood Name must be less than {{ limit }} characters")
     * @Assert\NotBlank(message = "Please specify a neighbourhood name")
     */
    private $neighbourhoodName;

    /**
     * @var string
     *
     * @ORM\Column(name="neighbourhoodId", type="string", length=25, nullable=true)
     *
     * @Assert\Length(max = 25, maxMessage = "Neighbourhood ID must be less than {{ limit }} characters")
     * /@Assert\GreaterThan(value = 0, message = "Please specify a valid neighbourhood id")
     */
    private $neighbourhoodId;

    /**
     * @var Address
     *
     * @ORM\ManyToOne(targetEntity="Address",cascade={"persist"})
     * @ORM\JoinColumn(name="addressId", referencedColumnName="id")
     *
     * @Assert\Valid()
     *
     */
    private $address;

    private $contacts;

    /**
     * @ORM\OneToMany(targetEntity="Container",cascade={"persist"}, mappedBy="property")
     */
    private $bins;
    private $buildings;

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
     * Set site id
     *
     * @param int $siteId
     *
     * @return Property
     */
    public function setSiteId($siteId)
    {
        $this->siteId = $siteId;

        return $this;
    }

    /**
     * Get site id
     *
     * @return int
     */
    public function getSiteId()
    {
        return $this->siteId;
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

    public function __toString(){
        return $this->address->__toString();
    }

    /**
     * Returns an array of valid choices for the property status.
     * @return string[]
     */
    public static function getStatuses()
    {
        return array(
            "Active"=>"Active",
            "Active (Partial Billing)"=>"Active (Partial Billing)",
            "Active (Declined)"=>"Active (Declined)",
            "Inactive (Renovation)"=>"Inactive (Renovation)",
            "Inactive (Pending)"=>"Inactive (Pending)"
        );
    }

    /**
     * Returns an array of valid choices for the property type.
     * @return string[]
     */
    public static function getTypes()
    {
        return array(
            "..."=>"",
            "Townhouse Apartment"=>"Townhouse Apartment",
            "Townhouse Condo"=>"Townhouse Condo",
            "High Rise Apartment"=>"High Rise Apartment",
            "Low Rise Apartment"=>"Low Rise Apartment",
            "High Rise Condo"=>"High Rise Condo",
            "Low Rise Condo"=>"Low Rise Condo",
            "Mixed Use Condo Apartment"=>"Mixed Use Condo Apartment",
            "Mixed Use Apartment Commercial"=>"Mixed Use Apartment Commercial"
        );
    }
}
