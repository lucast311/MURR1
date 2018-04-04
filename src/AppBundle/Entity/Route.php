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

    /**
     * @var int
     *
     * @ORM\Column(name="routeId", type="integer", unique=true)
     * @Assert\NotNull(message="Please specify a Route ID")
     * @Assert\GreaterThan(value=0, message="Route ID must be a value greater than 0")
     */
    private $routeId;

    /**
     * @var array
     *
     * @ORM\OneToMany(targetEntity="RoutePickup",cascade={"persist","refresh"}, mappedBy="route")
     * @ORM\OrderBy({"pickupOrder" = "ASC"})
     */
    private $pickups;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=10)
     *
     */
    private $startDate;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=10)
     *
     */
    private $endDate;

    //bool
    private $template = false;

    //TODO: $dateModified


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
     * @param integer $routeId
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
     * @return int
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

    public function setStartDate($date)
    {

    }
    public function setEndDate($date)
    {

    }

    public function getStartDate()
    {

    }
    public function getEndDate()
    {

    }


    public function setTemplate()
    {
        $this->template = true;
    }
    public function getTemplate()
    {
        return $this->template;
    }
}

