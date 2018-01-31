<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

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


}

