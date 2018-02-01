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
     * @Assert\NotNull(message="Please specify a route ID")
     * @Assert\GreaterThan(value=0, message="Route ID must be a value greater than 0")
     */
    private $routeId;

    /**
     * @var array
     *
     * @ORM\OneToMany(targetEntity="RoutePickup",cascade={"persist"}, mappedBy="route")
     * @ORM\OrderBy({"pickupOrder" = "ASC"})
     */
    private $pickups;


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
}

