<?php

namespace AppBundle\Entity;

use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\Mapping as ORM;
/**
 * OOPs
 * @ORM\Entity(repositoryClass="AppBundle\Repository\OOPsRepository")
 * @ORM\Table(name="oops")
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
     * @ORM\Column(type="string", length=10)
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
     * @Assert\File(maxSize="6000000", mimeTypes={"image/jpeg","image/png"}, mimeTypesMessage="Please upload an image in JPEG or PNG format")
     */
    private $imageFile;

    /**
     * @var string
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $imagePath;

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
     * Get id
     *
     * @return string
     */
    public function getId()
    {
        return $this->id;
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



    /**
     * Sets imageFile.
     *
     * @param UploadedFile $file
     */
    public function setImageFile(UploadedFile $imageFile = null)
    {
        $this->imageFile = $imageFile;
    }

    /**
     * Get imageFile.
     *
     * @return UploadedFile
     */
    public function getImageFile()
    {
        return $this->imageFile;
    }


    public function getAbsoluteImagePath()
    {
        return null === $this->imagePath
            ? null
            : $this->getImageUploadRootDir().'/'.$this->imagePath;
    }

    public function getWebImagePath()
    {
        return null === $this->imagePath
            ? null
            : $this->getImageUploadDir().'/'.$this->imagePath;
    }

    protected function getImageUploadRootDir()
    {
        // the absolute directory path where uploaded
        // documents should be saved
        return __DIR__.'/../../../uploads/'.$this->getImageUploadDir();
    }

    protected function getImageUploadDir()
    {
        // get rid of the __DIR__ so it doesn't screw up
        // when displaying uploaded doc/image in the view.
        return 'images';
    }




    public function upload()
    {
        // the file property can be empty if the field is not required
        if (null === $this->getImageFile()) {
            return;
        }

        // use the original file name here but you should
        // sanitize it at least to avoid any security issues

        // move takes the target directory and then the
        // target filename to move to
        $this->getImageFile()->move(
            $this->getImageUploadRootDir(),
            $this->getImageFile()->getClientOriginalName()
        );

        // set the path property to the filename where you've saved the file
        $this->imagePath = $this->getImageFile()->getClientOriginalName();

        // clean up the file property as you won't need it anymore
        $this->imageFile = null;
    }

}

