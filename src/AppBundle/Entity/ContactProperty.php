<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ContactProperty
 *
 * @ORM\Table(name="contact_property")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\ContactPropertyRepository")
 */
class ContactProperty
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
     * @ORM\ManyToOne(targetEntity="Property", cascade={"persist"})
     * @ORM\JoinColumn(name="property_id")
     *
     * @ORM\Column(name="property_id", type="integer")
     */
    private $propertyId;

    /**
     * @var int
     *
     * @ORM\ManyToOne(targetEntity="Contact", cascade={"persist"})
     * @ORM\JoinColumn(name="contact_id")
     * 
     * @ORM\Column(name="contact_id", type="integer")
     */
    private $contactId;


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
     * Set propertyId
     *
     * @param integer $propertyId
     *
     * @return ContactProperty
     */
    public function setPropertyId($propertyId)
    {
        $this->propertyId = $propertyId;

        return $this;
    }

    /**
     * Get propertyId
     *
     * @return int
     */
    public function getPropertyId()
    {
        return $this->propertyId;
    }

    /**
     * Set contactId
     *
     * @param integer $contactId
     *
     * @return ContactProperty
     */
    public function setContactId($contactId)
    {
        $this->contactId = $contactId;

        return $this;
    }

    /**
     * Get contactId
     *
     * @return int
     */
    public function getContactId()
    {
        return $this->contactId;
    }
}

