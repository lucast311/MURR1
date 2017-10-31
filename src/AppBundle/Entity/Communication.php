<?php

namespace AppBundle\Entity;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Communication
 * @ORM\Table(name="communication")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\CommunicationRepository")
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
     * @var \DateTime
     * @ORM\Column(type="datetime")
     * @Assert\NotBlank(message="Please select a date")
     * @Assert\NotNull(message="Please select a date")
     * @Assert\DateTime(message="Please select a valid date")
     * @Assert\LessThan("today", message="Please select a current or past date")
     *
     */
    private $date;

    /**
     * @var string
     * @ORM\Column(type="string", length=25)
     * @Assert\NotBlank(message="Please select a type of communication")
     * @Assert\Choice(callback="getTypes", message = "Please select a type of communication")
     */
    private $type;

    /**
     * @var string
     * @ORM\Column(type="string", length=8)
     * @Assert\NotBlank(message="Please select incoming or outgoing")
     * @Assert\Choice(callback="getMediums", message = "Please select incoming or outgoing")
     */
    private $medium;

    /**
     * @var int
     * @ORM\Column(type="integer")
     * @Assert\NotBlank(message="Please enter a contact")
     * @Assert\Choice(callback="getContacts", message = "Please enter a contact")
     */
    private $contact;

    /**
     * @var int
     * @ORM\Column(type="integer")
     * @Assert\NotBlank(message="Please select a property")
     * @Assert\Choice(callback="getProperties", message = "Please select a property")
     */
    private $property;

    /**
     * @var string
     * @ORM\Column(type="string", length=25)
     * @Assert\NotBlank(message="Please select a category")
     * @Assert\Choice(callback="getCategories", message = "Please select a category")
     */
    private $category;

    /**
     * @var string
     * @ORM\Column(type="string", length=2000)
     * @Assert\NotBlank(message="Please provide a brief description of the communication")
     * @Assert\Length(max = 2000,
     *                min = 50,
     *                maxMessage = "Please keep the description under {{ limit }} characters",
     *                minMessage = "Please provide a description of {{ limit }} characters or more"
     * )
     */
    private $description;

    /**
     * @var int
     * @ORM\Column(type="integer")
     */
    private $user;


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
     * @param \DateTime $date
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
     * @return \DateTime
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

    /**
     * Set contact
     *
     * @param integer $contact
     *
     * @return Communication
     */
    public function setContact($contact)
    {
        $this->contact = $contact;

        return $this;
    }

    /**
     * Get contact
     *
     * @return int
     */
    public function getContact()
    {
        return $this->contact;
    }

    /**
     * Set property
     *
     * @param integer $property
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
     * Set user
     *
     * @param int $description
     *
     * @return Communication
     */
    public function setUser($user)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return int
     */
    public function getUser()
    {
        return $this->user;
    }

    public static function getMediums()
    {
        return array ('Incoming' => 'incoming', 'Outgoing' => 'outgoing');
    }

    public static function getTypes()
    {
        return array ('In Person' => 'in person', 'Phone' => 'phone', 'Email' => 'email');
    }

    public static function getCategories()
    {
        return array ('Container' => 'container', 'Collection' => 'collection', 'Misc.' => 'misc');
    }

    public static function getContacts()
    {
        return array ('Resident' => -1, 'Linda Smith' => 1, 'John Snow' => 2 );
    }

    public static function getProperties()
    {
        return array ('N/A' => -1, 'Multi-Property' => -2, '123 Fake St.' => 1, '456 Fake St.' => 2);
    }
}

