<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

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
     *
     * @Assert\NotBlank(message = "First name cannot be left blank")
     *
     * @Assert\Length(max=50 , message = "Length can't be more than 50 characters long.")
     */
    private $firstName;

    /**
     * @var string
     *
     * @ORM\Column(name="lastName", type="string", length=50)
     *
     * @Assert\NotBlank(message = "Last name cannot be left blank")
     *
     * @Assert\Length(max=50 , message = "Length can't be more than 50 characters long.")
     */
    private $lastName;

    /**
     * @var string
     *
     * @ORM\Column(name="organization", type="string", length=100, nullable=true)
     *
     * @Assert\Length(max=100 , message = "Length can't be more than 100 characters long.")
     */
    private $organization;

    /**
     * @var string
     *
     * @ORM\Column(name="primaryPhone", type="string", length=12, nullable=true)
     *
     * @Assert\NotBlank(message = "Primary phone cannot be left blank")
     *
     * @Assert\Regex(pattern = "/^\d{3}-\d{3}-\d{4}$/", message = "Phone number must be in the format of ###-###-####")
     *
     *
     */
    private $primaryPhone;

    /**
     * @var int
     *
     * @ORM\Column(name="phoneExtention", type="integer", nullable=true)
     *
     * @Assert\Length(max=4 , message = "Length can't be more than 4 characters long.")
     */
    private $phoneExtention;

    /**
     * @var string
     *
     * @ORM\Column(name="secondaryPhone", type="string", length=12, nullable=true)
     *
     * @Assert\Regex(pattern = "/^\d{3}-\d{3}-\d{4}$/", message = "Phone number must be in the format of ###-###-####")
     */
    private $secondaryPhone;

    /**
     * @var string
     *
     * @ORM\Column(name="emailAddress", type="string", length=100)
     *
     * @Assert\NotBlank(message = "Email address cannot be left blank")
     *
     * @Assert\Email(message = "Email must be in the format of 'Example@example.com'")
     */
    private $emailAddress;

    /**
     * @var string
     *
     * @ORM\Column(name="fax", type="string", length=12, nullable=true)
     *
     * @Assert\Regex(pattern = "/^\d{3}-\d{3}-\d{4}$/", message = "Fax must be in the format of ###-###-####")
     *
     */
    private $fax;

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
     * Set primaryPhone
     *
     * @param string $primaryPhone
     *
     * @return Contact
     */
    public function setprimaryPhone($primaryPhone)
    {
        $this->primaryPhone = $primaryPhone;

        return $this;
    }

    /**
     * Get primaryPhone
     *
     * @return string
     */
    public function getprimaryPhone()
    {
        return $this->primaryPhone;
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
     * Set secondaryPhone
     *
     * @param string $secondaryPhone
     *
     * @return Contact
     */
    public function setsecondaryPhone($secondaryPhone)
    {
        $this->secondaryPhone = $secondaryPhone;

        return $this;
    }

    /**
     * Get secondaryPhone
     *
     * @return string
     */
    public function getsecondaryPhone()
    {
        return $this->secondaryPhone;
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
    public function setAddress($address)
    {
        $this->address = $address;

        return $this;
    }

    /**
     * Get addressReference
     *
     * @return \stdClass
     */
    public function getAddress()
    {
        return $this->address;
    }
}

