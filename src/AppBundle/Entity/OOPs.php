<?php

namespace AppBundle\Entity;

/**
 * OOPs
 */
class OOPs
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var string
     */
    private $binSerialNumber;

    /**
     * @var string
     */
    private $problemType;

    /**
     * @var string
     */
    private $status;

    /**
     * @var string
     */
    private $description;

    /**
     * @var \stdClass
     */
    private $image;

    /**
     * The constructor for OOPs notice objects
     * @param mixed $binSerialNumber the serial number of the bin
     * @param mixed $problemType the problem assiciated with the bin/OOPs notice
     * @param mixed $status the current status of the OOPs notice / response to the notice
     * @param mixed $description an optional description of the issue
     * @param mixed $image an optional image of the oops notice
     */
    function __construct( $binSerialNumber, $problemType, $status = 'not started', $description = '', $image = null )
    {
        $this->binSerialNumber = htmlentities($binSerialNumber);
        $this->problemType = htmlentities($problemType);
        $this->status = htmlentities($status);
        $this->description = htmlentities($description);
        $this->image = $image;
    }

    /**
     * Set binSerialNumber
     *
     * @param string $binSerialNumber
     *
     * @return OOPs
     */
    public function setBinSerialNumber($binSerialNumber)
    {
        $this->binSerialNumber = $binSerialNumber;

        return $this;
    }

    /**
     * Get binSerialNumber
     *
     * @return string
     */
    public function getBinSerialNumber()
    {
        return $this->binSerialNumber;
    }

    /**
     * Set problemType
     *
     * @param string $problemType
     *
     * @return OOPs
     */
    public function setProblemType($problemType)
    {
        $this->problemType = $problemType;

        return $this;
    }

    /**
     * Get problemType
     *
     * @return string
     */
    public function getProblemType()
    {
        return $this->problemType;
    }

    /**
     * Set status
     *
     * @param string $status
     *
     * @return OOPs
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set description
     *
     * @param string $description
     *
     * @return OOPs
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set image
     *
     * @param \stdClass $image
     *
     * @return OOPs
     */
    public function setImage($image)
    {
        $this->image = $image;

        return $this;
    }

    /**
     * Get image
     *
     * @return \stdClass
     */
    public function getImage()
    {
        return $this->image;
    }
}

