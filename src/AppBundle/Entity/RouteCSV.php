<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;

/**
 * RouteCSV
 *
 */
class RouteCSV
{

   /**
    * @Assert\NotBlank(message="Please enter a route ID!")
    * @Assert\Integer(message="Please enter an integer.")
    */
    private $routeId;

    /**
     * @Assert\File(
     *      maxSize = "1024k",
     *      maxSizeMessage = "The max route size is 1 MB.",
     *      mimeType = {"text/csv"},
     *      mimeTypesMessage = "Invalid route file type. Please upload a valid route CSV."
     * )
     */
    private $file;


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
     * @return RouteCSV
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
     * Set file
     *
     * @param File $file
     *
     * @return RouteCSV
     */
    public function setFile(File $file = null)
    {
        $this->file = $file;

        return $this;
    }

    /**
     * Get file
     *
     * @return File
     */
    public function getFile()
    {
        return $this->file;
    }
}

