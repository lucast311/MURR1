<?php
namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * EduMat
 *
 * @ORM\Table(name="edu_mat")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\EduMatRepository")
 */
class EduMat
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
     * @Assert\NotBlank(message="This field is required.")
     * @Assert\Length(max=30, maxMessage="The maximum number of characters for this field is 30.")
     * @Assert\Regex(pattern="/^[a-zA-Z ]{0,30}$/", message="You have entered invalid characters into the name field. Please use letter names only. Additional characters may be used in the description field.")
     *
     * @ORM\Column(name="name", type="string", length=30)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="status", type="string", length=30)
     */
    private $status;

    /**
     * @var \DateTime
     *
     * @Assert\LessThanOrEqual("today", message="Please select either a current or past date.")
     *
     * @ORM\Column(name="dateCreated", type="datetime", length=10)
     */
    private $dateCreated;

    /**
     * @var \DateTime
     *
     * @Assert\LessThanOrEqual("today", message="Please select either a current or past date.")
     *
     * // FIND OUT HOW TO SPECIFY THAT DATE CREATED CAN'T BE A DATE BEFORE DATE CREATED
     *
     * @ORM\Column(name="dateFinished", type="datetime", length=10, nullable=true)
     */
    private $dateFinished;

    /**
     * @var string
     *
     * @Assert\NotBlank(message="This field is required.")
     * @Assert\Length(max=50, maxMessage="The maximum number of characters for this field is 50.")
     * @Assert\Regex(pattern="/^[a-zA-Z 1-9]{0,50}$/", message="You have entered invalid characters into this field. Please use only alphanumeric values, and spaces.")
     *
     * @ORM\Column(name="recipient", type="string", length=50)
     */
    private $recipient;

    /**
     * @var string
     *
     * @Assert\Length(max=250, maxMessage="The maximum number of characters for this field is 250.")
     *
     * @ORM\Column(name="description", type="string", length=250, nullable=true)
     */
    private $description;

    /**
     * @var string
     *
     * @ORM\Column(name="operationType", type="string", length=6, nullable=false)
     */
    private $operationType;


    function __construct($name="", $status="", $dateCreated="", $dateFinished="", $recipient="", $description="", $operationType="EduMat")
    {
        if($dateCreated == "")
        {
            $this->dateCreated = new \DateTime;
        }
        else
        {
            $this->dateCreated = $dateCreated;
        }

        if($dateFinished == "")
        {
            $this->dateFinished = new \DateTime;
        }
        else
        {
            $this->dateFinished = $dateFinished;
        }

        $this->name = $name;
        $this->status = $status;
        $this->recipient = $recipient;
        $this->description = $description;
        $this->operationType = $operationType;
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
     * Set name
     *
     * @param string $name
     *
     * @return EduMat
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set status
     *
     * @param string $status
     *
     * @return EduMat
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set dateCreated
     *
     * @param string $dateCreated
     *
     * @return EduMat
     */
    public function setDateCreated($date)
    {
        $this->dateCreated = $date;

        return $this;
    }

    /**
     * Get dateCreated
     *
     * @return \DateTime
     */
    public function getDateCreated()
    {
        return $this->dateCreated;
    }

    /**
     * Set dateFinished
     *
     * @param string $dateFinished
     *
     * @return EduMat
     */
    public function setDateFinished($date)
    {
        $this->dateFinished = $date;

        return $this;
    }

    /**
     * Get dateFinished
     *
     * @return \DateTime
     */
    public function getDateFinished()
    {
        return $this->dateFinished;
    }

    /**
     * Set recipient
     *
     * @param string $recipient
     *
     * @return EduMat
     */
    public function setRecipient($recipient)
    {
        $this->recipient = $recipient;

        return $this;
    }

    /**
     * Get recipient
     *
     * @return string
     */
    public function getRecipient()
    {
        return $this->recipient;
    }

    /**
     * Set description
     *
     * @param string $description
     *
     * @return EduMat
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
     * Get operationType
     *
     * We don't need a setter for this, since it will auto generate
     *  if we create it in the story14a functionality
     *
     * @return string
     */
    public function getOperationType()
    {
        return $this->operationType;
    }

    /**
     * Set operationType
     *
     * @param string $operationType
     *
     * @return EduMat
     */
    public function setOperationType($operationType)
    {
        $this->operationType = $operationType;

        return $this;
    }
}
