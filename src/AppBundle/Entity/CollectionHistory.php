<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use \DateTime; 

/**
 * CollectionHistory
 *
 * @ORM\Table(name="collection_history")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\CollectionHistoryRepository")
 */
class CollectionHistory
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
     * @ORM\ManyToOne(targetEntity="Container", inversedBy="ContainerSerial", cascade={"persist"})
     * @ORM\JoinColumn(name="ContainerID", referencedColumnName="id")
     * @Assert\NotBlank(message="Please select a bin")
     */
    private $containerId;

    /**
     * @var bool
     *
     * @ORM\Column(name="notCollected", type="boolean", nullable=true)
     * @Assert\Type("bool")
     */
    private $notCollected;

    /**
     * @var string
     *
     * @ORM\Column(name="notes", type="string", length=250, nullable=true)
     * @Assert\Length(max=250, maxMessage="Notes can only be less than 250 characters")
     */
    private $notes;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="dateCollected", type="datetime", nullable=false)
     * @Assert\NotBlank(message="Please enter the date collected")
     * @Assert\LessThanOrEqual("today", message="Date cannot be a date in the future")
     * @Assert\Date(message="Please select a valid date")
     */
    private $dateCollected;



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
     * Set containerId
     *
     * @param integer $containerId
     *
     * @return CollectionHistory
     */
    public function setContainerId($containerId)
    {
        $this->containerId = $containerId;

        return $this;
    }

    /**
     * Get containerId
     *
     * @return int
     */
    public function getContainerId()
    {
        return $this->containerId;
    }

    /**
     * Set notCollected
     *
     * @param boolean $notCollected
     *
     * @return CollectionHistory
     */
    public function setNotCollected($notCollected)
    {
        $this->notCollected = $notCollected;

        return $this;
    }

    /**
     * Get notCollected
     *
     * @return bool
     */
    public function getNotCollected()
    {
        return $this->notCollected;
    }

    /**
     * Set notes
     *
     * @param string $notes
     *
     * @return CollectionHistory
     */
    public function setNotes($notes)
    {
        $this->notes = $notes;

        return $this;
    }

    /**
     * Get notes
     *
     * @return string
     */
    public function getNotes()
    {
        return $this->notes;
    }

    /**
     * Get date
     *
     * @return \Date
     */
    public function getDateCollected()
    {
        return $this->dateCollected;
    }

    /**
     * Set date
     *
     * @param \DateTime $dateCollected
     *
     * @return CollectionHistory
     */
    public function setDateCollected($dateCollected)
    {
        $this->dateCollected = $dateCollected;

        return $this;
    }


}

