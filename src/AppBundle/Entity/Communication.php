<?php

namespace AppBundle\Entity;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use DateTime;

/**
 * This class is responsible for modelling a communication between a user
 * and a contact
 *
 * @ORM\Table(name="communication")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\CommunicationRepository")
 * @ORM\HasLifecycleCallbacks
 */
class Communication
{
    /**
     * @var int
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     * @ORM\Column(type="string", length=10)
     * @Assert\NotBlank(message="Please select a date")
     * @Assert\NotNull(message="Please select a date")
     *
     */
    private $date;

    /**
     * @var string
     * @ORM\Column(type="string", length=25)
     * @Assert\NotBlank(message="Please select a type of communication")
     * @Assert\Choice(strict=true, callback="getTypes", message = "Please select a type of communication")
     */
    private $type;

    /**
     * @var string
     * @ORM\Column(type="string", length=8)
     * @Assert\NotBlank(message="Please select a direction")
     * @Assert\Choice(strict=true, callback="getMediums", message = "Please select a direction")
     */
    private $medium;

    ///**
    // * @var int
    // * @ORM\Column(type="integer", nullable=true)
    // * @Assert\NotBlank(message="Please enter a contact")
    // * @Assert\Choice(strict=true, callback="getContacts", message = "Please enter a contact")
    // */
    //private $contact;

    /**
     * Summary of $contactName
     * @var mixed
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\Length(max=255,
     *                  maxMessage = "Contact name must be less than {{ limit }} characters")
     */
    private $contactName;
    /**
     * Summary of $contactEmail
     * @var mixed
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\Length(max=255,
     *                  maxMessage = "Contact email must be less than {{ limit }} characters")
     * @Assert\Email(message = "Email must be in the format of 'Example@example.com'")
     */
    private $contactEmail;
    /**
     * Summary of $contactPhone
     * @var mixed
     * @ORM\Column(type="string", length=12, nullable=true)
     * @Assert\Regex(pattern = "/^\d{3}-\d{3}-\d{4}$/", message = "Phone number must be in the format of ###-###-####")
     */
    private $contactPhone;

    ///**
    // * @var Property
    // *
    // * CHANGE ME FOR STORY 11b
    // *
    // * @ORM\Column(type="integer", nullable=true)
    // * @Assert\NotBlank(message="Please select a property")
    // * @Assert\Choice(strict=true, callback="getProperties", message = "Please select a property")
    // */

    /**
     * @ORM\ManyToOne(targetEntity="Property", cascade={"persist"}, inversedBy="communications")
     * @ORM\JoinColumn(name="propertyId", referencedColumnName="id")
     * @var mixed
     */
    private $property;

    /**
     * @var string
     * @ORM\Column(type="string", length=25)
     * @Assert\NotBlank(message="Please select a category")
     * @Assert\Choice(strict=true, callback="getCategories", message = "Please select a category")
     */
    private $category;

    /**
     * @var string
     * @ORM\Column(type="string", length=500)
     * @Assert\NotBlank(message="Please provide a brief description of the communication")
     * @Assert\Length(max = 500,
     *                maxMessage = "Description must be {{ limit }} characters or less")
     */
    private $description;

    /**
     * @ORM\Column(name="dateModified", type="datetime")
     * @var mixed
     */
    protected $dateModified;

    /**
     * Default constructor for a Communication object. This will just set the value of date to be today by default
     */
    public function __construct()
    {
        $tempDate = new DateTime('now');
        $this->date = $tempDate->format('Y-m-d');

        if($this->getDateModified() == NULL)
        {
            $this->setDateModified(new \DateTime());
        }
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
     * Set date
     *
     * @param string $date
     *
     * @return Communication
     */
    public function setDate($date)
    {
        $this->date = $date;
        return $this;
    }

    /**
     * Get date
     *
     * @return string
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Set type
     *
     * @param string $type
     *
     * @return Communication
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
     * Set medium
     *
     * @param string $medium
     *
     * @return Communication
     */
    public function setMedium($medium)
    {
        $this->medium = $medium;

        return $this;
    }

    /**
     * Get medium
     *
     * @return string
     */
    public function getMedium()
    {
        return $this->medium;
    }

    ///**
    // * Set contact
    // *
    // * @param integer $contact
    // *
    // * @return Communication
    // */
    //public function setContact($contact)
    //{
    //    $this->contact = $contact;

    //    return $this;
    //}

    ///**
    // * Get contact
    // *
    // * @return int
    // */
    //public function getContact()
    //{
    //    return $this->contact;
    //}

    /**
     * Story 11b
     * @param mixed $name
     */
    public function setContactName($name){
        $this->contactName = $name;
        return $this;
    }

    /**
     * Story 11b
     */
    public function getContactName(){
        return $this->contactName;
    }

    /**
     * Story 11b
     * @param mixed $email
     */
    public function setContactEmail($email){
        $this->contactEmail = $email;
        return $this;
    }

    /**
     * Story 11b
     */
    public function getContactEmail(){
        return $this->contactEmail;
    }

    /**
     * Story 11b
     * @param mixed $phone
     */
    public function setContactPhone($phone){
        $this->contactPhone = $phone;
        return $this;
    }

    /**
     * Story 11b
     * @param mixed $phone
     */
    public function getContactPhone(){
        return $this->contactPhone;
    }

    /**
     * Set property
     *
     * @param Property $property
     *
     * @return Communication
     */
    public function setProperty($property)
    {
        $this->property = $property;

        return $this;
    }

    /**
     * Get property
     *
     * @return int
     */
    public function getProperty()
    {
        return $this->property;
    }

    /**
     * Set category
     *
     * @param string $category
     *
     * @return Communication
     */
    public function setCategory($category)
    {
        $this->category = $category;

        return $this;
    }

    /**
     * Get category
     *
     * @return string
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * Set description
     *
     * @param string $description
     *
     * @return Communication
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
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

    ///**
    // * Set user
    // *
    // * @param int $description
    // *
    // * @return Communication
    // */
    //public function setUser($user)
    //{
    //    $this->user = $user;

    //    return $this;
    //}

    ///**
    // * Get user
    // *
    // * @return int
    // */
    //public function getUser()
    //{
    //    return $this->user;
    //}

    /**
     * This method will return the valid values for the medium field
     * @return string[]
     */
    public static function getMediums()
    {
        return array ('Incoming' => 'Incoming', 'Outgoing' => 'Outgoing', 'Onsite' => 'Onsite');
    }

    /**
     * This method will return the valid values for the type field
     * @return string[]
     */
    public static function getTypes()
    {
        return array ('In Person' => 'In Person', 'Phone' => 'Phone', 'Email' => 'Email');
    }

    /**
     * This method will return the valid values for the Category field
     * @return string[]
     */
    public static function getCategories()
    {
        return array ('Container' => 'Container', 'Collection' => 'Collection', 'Misc' => 'Misc');
    }

    ///**
    // * This method will return the valid values for the Contacts field
    // * @return array
    // */
    //public static function getContacts()
    //{
    //    return array ('Resident' => -1, 'Linda Smith' => 1, 'John Snow' => 2 );
    //}

    ///**
    // * This method will return the valid values for the properties field
    // * @return array
    // */
    //public static function getProperties()
    //{
    //    return array ('N/A' => -1, 'Multi-Property' => -2, '123 Fake St.' => 1, '456 Fake St.' => 2);
    //}

    public function __toString()
    {
        if($this->date != "")
        {
            return $this->date;
        }
        else
        {
            return "";
        }
    }
}
