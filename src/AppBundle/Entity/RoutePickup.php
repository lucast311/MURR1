<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

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
     * @var Route
     *
     * @ORM\ManyToOne(targetEntity="Route", inversedBy="pickups", cascade={"persist"})
     * @ORM\JoinColumn(name="routeId", referencedColumnName="id")
     */
    private $route;

    /**
     * @var Container
     * @ORM\ManyToOne(targetEntity="Container", cascade={"persist"})
     * @ORM\JoinColumn(name="containerId", referencedColumnName="id")
     */
    private $container;

    /**
     * @var int
     *
     * @ORM\Column(name="pickupOrder", type="integer")
     * @Assert\NotNull(message="Please specify a pickup order")
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
     * @param Route $route
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
     * @return Route
     */
    public function getRoute()
    {
        return $this->route;
    }

    /**
     * Set container
     *
     * @param Container $container
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
     * @return Container
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

