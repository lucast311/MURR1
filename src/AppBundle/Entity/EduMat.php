<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

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
     * @var string
     *
     * @ORM\Column(name="dateCreated", type="string", length=10)
     */
    private $dateCreated;

    /**
     * @var string
     *
     * @ORM\Column(name="dateFinished", type="string", length=10, nullable=true)
     */
    private $dateFinished;

    /**
     * @var string
     *
     * @ORM\Column(name="recipient", type="string", length=50)
     */
    private $recipient;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="string", length=250, nullable=true)
     */
    private $description;


    function __construct($name, $status="", $dateCreated="", $dateFinished="", $recipient="", $description="")
    {
        $this->name = $name;
        $this->status = $status;
        $this->dateCreated= $dateCreated;
        $this->dateFinished = $dateFinished;
        $this->recipient = $recipient;
        $this->description = $description;
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
    public function setDateCreated($dateCreated)
    {
        $this->dateCreated = $dateCreated;

        return $this;
    }

    /**
     * Get dateCreated
     *
     * @return string
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
    public function setDateFinished($dateFinished)
    {
        $this->dateFinished = $dateFinished;

        return $this;
    }

    /**
     * Get dateFinished
     *
     * @return string
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
}

