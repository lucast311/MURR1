<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
/**
 * Route
 *
 * @ORM\Table(name="route")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\RouteRepository")
 * @UniqueEntity(fields = {"routeId"}, message = "Route ID Already exists")
 */
class Route
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;


    //S40C: changed int -> String for storing template names + added group validators
    /**
     * @var string
     *
     * @ORM\Column(name="routeId", type="string", length=20, unique=true)
     * @Assert\Length(min=1,max=6,
     *          minMessage = "Route ID must be atleast {{ limit }} digits long",
     *          maxMessage = "Route ID can not be more than {{ limit }} digits long",
     *          groups={"route"})
     * @Assert\Length(min=1,max=20,
     *          minMessage = "Template name must be atleast {{ limit }} characters long",
     *          maxMessage = "Template name can not be more than {{ limit }} characters long",
     *          groups={"template"})
     * @Assert\NotNull(message="Please specify a Route ID",
     *          groups={"route"})
     * @Assert\NotNull(message="Please specify a Template name",
     *          groups={"template"})
     * @Assert\Regex(
     *          pattern="/^[0-9]*$/",
     *          htmlPattern=".*",
     *          message="The Route ID must contain 1 to 6 digits, no letters",
     *          groups={"route"})
     */
    private $routeId;

    /**
     * @var array
     *
     * @ORM\OneToMany(targetEntity="RoutePickup",cascade={"persist","refresh"}, mappedBy="route")
     * @ORM\OrderBy({"pickupOrder" = "ASC"})
     */
    private $pickups;

    //S40C
    /**
     * @var mixed
     *
     * @ORM\Column(name="startDate", type="datetime", nullable=true)
     * @Assert\DateTime()
     */
    private $startDate;

    /**
     * @var mixed
     *
     * @ORM\Column(name="endDate", type="datetime", nullable=true)
     * @Assert\DateTime()
     */
    private $endDate;

    /**
     * @var boolean
     *
     * @ORM\Column(name="template", type="boolean", options={"default":"0"})
     */
    private $template;

    /**
     * @var mixed
     *
     * @ORM\Column(name="dateModified", type="datetime")
     *
     */
    protected $dateModified;
    //End S40C

    /**
     * S40C
     * init a normal Route
     */
    public function __construct()
    {
        if($this->getDateModified() == Null)
        {
            $this->setDateModified(new \DateTime());
        }

        if($this->getTemplate() == Null)
        {
            $this->setTemplate(false);
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
     * Set routeId
     *
     * @param String $routeId
     *
     * @return Route
     */
    public function setRouteId($routeId)
    {
        $this->routeId = $routeId;

        return $this;
    }

    /**
     * Get routeId
     *
     * @return String
     */
    public function getRouteId()
    {
        return $this->routeId;
    }

    /**
     * Set pickups
     *
     * @param array $pickups
     *
     * @return Route
     */
    public function setPickups($pickups)
    {
        $this->pickups = $pickups;

        return $this;
    }

    /**
     * Get pickups
     *
     * @return array
     */
    public function getPickups()
    {
        return $this->pickups;
    }


    //S40C: New functions
    /**
     * Set startDate
     *
     * @param mixed $date
     *
     * @return Route
     */
    public function setStartDate($date)
    {
        $this->startDate = $date;
        return $this;
    }

    /**
     * Set endDate
     *
     * @param mixed $date
     *
     * @return Route
     */
    public function setEndDate($date)
    {
        $this->endDate = $date;
        return $this;
    }

    /**
     * Get startDate
     *
     * @return mixed
     */
    public function getStartDate()
    {
        return $this->startDate;
    }

    /**
     * Get endDate
     *
     * @return mixed
     */
    public function getEndDate()
    {
        return $this->endDate;
    }


    /**
     * Set template
     * Used to turn this into a template, or specify otherwise hacky but works
     *
     * @param boolean $template
     *
     * @return Route
     */
    public function setTemplate($template = true)
    {
        $this->template = $template;
        return $this;
    }

    /**
     * Get template
     *
     * @return boolean
     */
    public function getTemplate()
    {
        return $this->template;
    }


    /**
     * @ORM\PrePersist()
     * @ORM\PreUpdate()
     */
    public function updateModifiedDatetime()
    {
        $this->setDateModified(new \DateTime());
    }

    /**
     * Set dateModified
     *
     * @param \DateTime $dateModified
     * @return Route
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

    public function __toString(){
        //if($this->getTemplate())
        //{
            return $this->getRouteId();
        //}
    }
    //End S40C: New functions
}

