<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * RoutePickup
 *
 * @ORM\Table(name="route_pickup")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\RoutePickupRepository")
 */
class RoutePickup
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
     * @var \stdClass
     *
     * @ORM\Column(name="route", type="object")
     */
    private $route;

    /**
     * @var \stdClass
     *
     * @ORM\Column(name="container", type="object")
     */
    private $container;

    /**
     * @var int
     *
     * @ORM\Column(name="pickupOrder", type="integer")
     */
    private $pickupOrder;


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
     * Set route
     *
     * @param \stdClass $route
     *
     * @return RoutePickup
     */
    public function setRoute($route)
    {
        $this->route = $route;

        return $this;
    }

    /**
     * Get route
     *
     * @return \stdClass
     */
    public function getRoute()
    {
        return $this->route;
    }

    /**
     * Set container
     *
     * @param \stdClass $container
     *
     * @return RoutePickup
     */
    public function setContainer($container)
    {
        $this->container = $container;

        return $this;
    }

    /**
     * Get container
     *
     * @return \stdClass
     */
    public function getContainer()
    {
        return $this->container;
    }

    /**
     * Set pickupOrder
     *
     * @param integer $pickupOrder
     *
     * @return RoutePickup
     */
    public function setPickupOrder($pickupOrder)
    {
        $this->pickupOrder = $pickupOrder;

        return $this;
    }

    /**
     * Get pickupOrder
     *
     * @return int
     */
    public function getPickupOrder()
    {
        return $this->pickupOrder;
    }
}

