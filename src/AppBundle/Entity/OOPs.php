<?php

namespace AppBundle\Entity;

use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\Mapping as ORM;
/**
 * OOPs
 * @ORM\Entity
 */
class OOPs
{
    /**
     * @var int
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     * @ORM\Column(type="string", length=10, unique=true)
     * @Assert\NotBlank(message="Please enter a serial number")
     * @Assert\Length(max = 10,
     *                min = 10,
     *                maxMessage = "Please enter a valid serial number with 10 characters",
     *                minMessage = "Please enter a valid serial number with 10 characters"
     * )
     */
    private $binSerial;

    /**
     * @var string
     * @ORM\Column(type="string")
     * @Assert\Choice(callback="getProblemOptions", message = "Please select a problem type")
     */
    private $problemType;

    /** // replace getProblemOptions with getStatusOptions
     * @var string
     * @ORM\Column(type="string")
     * @Assert\Choice(callback="getStatusOptions", message = "Please select the current OOPs status")
     */
    private $status;

    /**
     * @var string
     * @ORM\Column(type="string", length=250, nullable=true)
     * @Assert\Length(max = 250,
     *                maxMessage = "Please enter a valid description with less than {{ limit }} characters"
     *                )
     */
    private $description;

    /**
     * @var \stdClass
     * @ORM\Column(type="object", nullable=true)
     * @Assert\Image(mimeTypes="image/jpeg", mimeTypesMessage="Please upload an image in JPEG or PNG format")
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
    function __construct( $binSerial, $problemType, $status = 'not in progress', $description = '', $image = null )
    {
        $this->binSerial = htmlentities($binSerial);
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
    public function setBinSerial($binSerial)
    {
        $this->binSerial = $binSerial;

        return $this;
    }

    /**
     * Get binSerialNumber
     *
     * @return string
     */
    public function getBinSerial()
    {
        return $this->binSerial;
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


    public static function getStatusOptions()
    {
        return array('Not yet started' => 'Not yet started',
                     'In progress' => 'In progress',
                     'Completed' => 'Completed');
    }

    public static function getProblemOptions()
    {
        return array ('Damage' => 'Damage',
                      'Contamination' => 'Contamination',
                      'Blocked' => 'Blocked',
                      'Other (include in description)' => 'Other' );
    }

    public static function getValidImageTypes()
    {
        return array ('image/png','image/jpeg');
    }
}

