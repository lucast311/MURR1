<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * RoutePickup
 *
 * @ORM\Table(name="RoutePickup")
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
     * @ORM\ManyToOne(targetEntity="Route", inversedBy="pickups", cascade={"persist","refresh"})
     * @ORM\JoinColumn(name="routeId", referencedColumnName="id")
     * @Assert\NotNull(message="Please specify a route")
     */
    private $route;

    /**
     * @var Container
     * @ORM\ManyToOne(targetEntity="Container", cascade={"persist"})
     * @ORM\JoinColumn(name="containerId", referencedColumnName="id", onDelete="CASCADE")
     * @Assert\NotNull(message="Please specify a container")
     */
    private $container;

    /**
     * @var int
     *
     * @ORM\Column(name="pickupOrder", type="integer")
     * @Assert\NotNull(message="Please specify a pickup order")
     * @Assert\GreaterThan(value=0, message="Pickup order must be greater than 0")
     */
    private $pickupOrder;


    /** S40B
     * @var Truck
     * @ORM\ManyToOne(targetEntity="Truck", cascade={"persist"})
     * @ORM\JoinColumn(name="truckId", referencedColumnName="id", onDelete="CASCADE")
     */
    private $truck;


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


    /** S40B
     * Set truck
     * @param Truck $truck
     * @return RoutePickup
     */
    public function setTruck($truck)
    {
        $this->truck = $truck;
        return $this;
    }

    /** S40B
     * Get truck
     * @return Truck
     */
    public function getTruck()
    {
        return $this->truck;
    }
}

