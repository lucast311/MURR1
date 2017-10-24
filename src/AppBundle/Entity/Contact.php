<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Contact
 *
 * @ORM\Table(name="contact")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\ContactRepository")
 */
class Contact
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
     * @ORM\Column(name="firstName", type="string", length=50)
     */
    private $firstName;

    /**
     * @var string
     *
     * @ORM\Column(name="lastName", type="string", length=50)
     */
    private $lastName;

    /**
     * @var string
     *
     * @ORM\Column(name="organization", type="string", length=100, nullable=true)
     */
    private $organization;

    /**
     * @var string
     *
     * @ORM\Column(name="officePhone", type="string", length=12, nullable=true)
     */
    private $officePhone;

    /**
     * @var int
     *
     * @ORM\Column(name="phoneExtention", type="integer", nullable=true)
     */
    private $phoneExtention;

    /**
     * @var string
     *
     * @ORM\Column(name="mobilePhone", type="string", length=12, nullable=true)
     */
    private $mobilePhone;

    /**
     * @var string
     *
     * @ORM\Column(name="emailAddress", type="string", length=100)
     */
    private $emailAddress;

    /**
     * @var string
     *
     * @ORM\Column(name="fax", type="string", length=12, nullable=true)
     */
    private $fax;

    /**
     * @var \stdClass
     *
     * @ORM\ManyToOne(targetEntity="Address")
     * @ORM\JoinColumn(name="addressId", referencedColumnName="id")
     * 
     * @ORM\Column(name="addressId", type="integer")
     */
    private $addressId;


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
     * Set firstName
     *
     * @param string $firstName
     *
     * @return Contact
     */
    public function setFirstName($firstName)
    {
        $this->firstName = $firstName;

        return $this;
    }

    /**
     * Get firstName
     *
     * @return string
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * Set lastName
     *
     * @param string $lastName
     *
     * @return Contact
     */
    public function setLastName($lastName)
    {
        $this->lastName = $lastName;

        return $this;
    }

    /**
     * Get lastName
     *
     * @return string
     */
    public function getLastName()
    {
        return $this->lastName;
    }

    /**
     * Set organization
     *
     * @param string $organization
     *
     * @return Contact
     */
    public function setOrganization($organization)
    {
        $this->organization = $organization;

        return $this;
    }

    /**
     * Get organization
     *
     * @return string
     */
    public function getOrganization()
    {
        return $this->organization;
    }

    /**
     * Set officePhone
     *
     * @param string $officePhone
     *
     * @return Contact
     */
    public function setOfficePhone($officePhone)
    {
        $this->officePhone = $officePhone;

        return $this;
    }

    /**
     * Get officePhone
     *
     * @return string
     */
    public function getOfficePhone()
    {
        return $this->officePhone;
    }

    /**
     * Set phoneExtention
     *
     * @param integer $phoneExtention
     *
     * @return Contact
     */
    public function setPhoneExtention($phoneExtention)
    {
        $this->phoneExtention = $phoneExtention;

        return $this;
    }

    /**
     * Get phoneExtention
     *
     * @return int
     */
    public function getPhoneExtention()
    {
        return $this->phoneExtention;
    }

    /**
     * Set mobilePhone
     *
     * @param string $mobilePhone
     *
     * @return Contact
     */
    public function setMobilePhone($mobilePhone)
    {
        $this->mobilePhone = $mobilePhone;

        return $this;
    }

    /**
     * Get mobilePhone
     *
     * @return string
     */
    public function getMobilePhone()
    {
        return $this->mobilePhone;
    }

    /**
     * Set emailAddress
     *
     * @param string $emailAddress
     *
     * @return Contact
     */
    public function setEmailAddress($emailAddress)
    {
        $this->emailAddress = $emailAddress;

        return $this;
    }

    /**
     * Get emailAddress
     *
     * @return string
     */
    public function getEmailAddress()
    {
        return $this->emailAddress;
    }

    /**
     * Set fax
     *
     * @param string $fax
     *
     * @return Contact
     */
    public function setFax($fax)
    {
        $this->fax = $fax;

        return $this;
    }

    /**
     * Get fax
     *
     * @return string
     */
    public function getFax()
    {
        return $this->fax;
    }

    /**
     * Set addressReference
     *
     * @param \stdClass $addressReference
     *
     * @return Contact
     */
    public function setAddressReference($addressReference)
    {
        $this->addressReference = $addressReference;

        return $this;
    }

    /**
     * Get addressReference
     *
     * @return \stdClass
     */
    public function getAddressId()
    {
        return $this->addressId;
    }
}

