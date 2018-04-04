<?php

namespace AppBundle\Entity;


use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\ExecutionContextInterface;
use Symfony\Component\Validator\Constraints as Assert;
use AppBundle\Validator\Constraints as AcmeAssert;
use Doctrine\Common\Collections\ArrayCollection;


//
//

/**
 * Contact
 *
 * @ORM\Table(name="contact")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\ContactRepository")
 * @ORM\HasLifecycleCallbacks
 * @AcmeAssert\ContactAtLeastOneField
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
     * @ORM\Column(name="firstName", type="string", length=100, nullable=true)
     *
     *
     * @Assert\Length(max=100 , maxMessage = "Length can't be more than 100 characters long.")
     */
    private $firstName;

    /**
     * @var string
     *
     * @ORM\Column(name="lastName", type="string", length=100, nullable=true)
     *
     *
     * @Assert\Length(max=100 , maxMessage = "Length can't be more than 100 characters long.")
     */
    private $lastName;

    /**
     * @var string
     *
     * @ORM\Column(name="role", type="string", length=100, nullable=false)
     *
     * @Assert\Choice(strict=true, callback="roleOptions", message = "Please select only choices in the 'Role' dropdown box")
     */
    private $role;

    /**
     * @var string
     *
     * @ORM\Column(name="companyName", type="string", length=100, nullable=true)
     *
     * @Assert\Length(max=100 , maxMessage = "Company name can't be more than 100 characters long.")
     */
    private $companyName;

    /**
     * @var string
     *
     * @ORM\Column(name="primaryPhone", type="string", length=12, nullable=true)
     *
     *
     * @Assert\Regex(pattern = "/^\d{3}-\d{3}-\d{4}$/", message = "Phone number must be in the format of ###-###-####")
     *
     */
    private $primaryPhone;

    /**
     * @var int
     *
     * @ORM\Column(name="phoneExtension", type="integer", nullable=true)
     *
     * @Assert\Regex(pattern = "/^\d{4}$/", message = "Phone Extension must be in the format of ####")
     */
    private $phoneExtension;

    /**
     * @var string
     *
     * @ORM\Column(name="secondaryPhone", type="string", length=12, nullable=true)
     *
     * @Assert\Regex(pattern = "/^\d{3}-\d{3}-\d{4}$/", message = "Phone number must be in the format of ###-###-####")
     *
     *
     */
    private $secondaryPhone;

    /**
     * @var string
     *
     * @ORM\Column(name="emailAddress", type="string", length=100, nullable=true)
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
     * @ORM\ManyToOne(targetEntity="Address", cascade={"persist"})
     * @ORM\JoinColumn(name="addressId", referencedColumnName="id")
     *
     * @Assert\Valid()
     */
    private $address;

    /**
     * Contacts have many properties
     *@ORM\ManyToMany(targetEntity="Property", inversedBy="contacts", cascade={"persist", "refresh"}, fetch="EAGER")
     *@ORM\JoinTable(name="contact_properties")
     *@var ArrayCollection
     */
    private $properties;

    /**
     * @ORM\Column(name="dateModified", type="datetime")
     * @var mixed
     */
    protected $dateModified;

    public function __construct()
    {
        $this->properties = new \Doctrine\Common\Collections\ArrayCollection();

        if($this->getDateModified() == NULL)
        {
            $this->setDateModified(new \DateTime());
        }
    }

    /**
     * returns the options that a role can be
     * @return string[]
     */
    public static function roleOptions()
    {
        return array('Property Manager' => 'Property Manager',
                     'Property Manager Staff' => 'Property Manager Staff',
                     'Onsite Staff' => 'Onsite Staff',
                     'Owner' => 'Owner',
                     'Landlord' => 'Landlord',
                     'Condo President' => 'Condo President',
                     'Condo Board Member' => 'Condo Board Member',
                     'Mailing' => 'Mailing'
            );
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
     * Set role
     *
     * @param string $role
     *
     * @return Contact
     */
    public function setRole($role)
    {
        $this->role = $role;

        return $this;
    }

    /**
     * Get role
     *
     * @return string
     */
    public function getRole()
    {
        return $this->role;
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
     * Set phoneExtension
     *
     * @param integer $phoneExtension
     *
     * @return Contact
     */
    public function setPhoneExtension($phoneExtension)
    {
        $this->phoneExtension = $phoneExtension;

        return $this;
    }

    /**
     * Get phoneExtension
     *
     * @return int
     */
    public function getPhoneExtension()
    {
        return $this->phoneExtension;
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

    /**
     * sets the company name
     * @param mixed $companyName
     * @return Contact
     */
    public function setCompanyName($companyName)
    {
        $this->companyName = $companyName;
        return $this;
    }

    public function getCompanyName()
    {
        return $this->companyName;
    }
    /**
     * gets the associated properties
     * @return ArrayCollection
     */
    public function getProperties()
    {
        $iterator = $this->properties->getIterator();


        //HACK: Don't do this again. This was a special case when dealing with Properties and sorting by address.
        $iterator->uasort(
            function($a, $b){
                return ($a->getAddress()->getStreetAddress() < $b->getAddress()->getStreetAddress()) ? -1 : 1;
            });

        return new ArrayCollection(iterator_to_array($iterator));
    }
    /**
     * Sets the associated properties
     * @param ArrayCollection $properties
     */
    public function setProperties($properties)
    {
        $this->properties = $properties;
    }

    /**
     * Set dateModified
     *
     * @param \DateTime $dateModified
     * @return Communication
     */
    public function setDateModified($dateModified)
    {
        $this->dateModified = $dateModified;

        return $this;
    }

    /**
     * Get dateModified
     *
     * @return \DateTime
     */
    public function getDateModified()
    {
        return $this->dateModified;
    }

    /**
     * @ORM\PrePersist()
     * @ORM\PreUpdate()
     */
    public function updateModifiedDatetime()
    {
        $this->setDateModified(new \DateTime());
    }

    public function __toString()
    {
        return $this->lastName . ", " . $this->firstName;
    }
}