<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Route
 *
 * @ORM\Table(name="route")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\RouteRepository")
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
     */
    private $routeId;

    /**
     * @var array
     *
     * @ORM\Column(name="pickups", type="array", nullable=true)
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

