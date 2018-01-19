<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Address
 *
 * @ORM\Table(name="address")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\AddressRepository")
 */
class Address
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
     * @var string
     *
     * @ORM\Column(name="streetAddress", type="string", length=150)
     *
     * @Assert\NotBlank(message = "Street address cannot be left blank")
     *
     * @Assert\Length(max=150 , maxMessage = "Length can't be more than 150 characters long.")
     */
    private $streetAddress;


    /**
     * @var string
     *
     * @ORM\Column(name="postalCode", type="string", length=7)
     *
     * @Assert\NotBlank(message = "Postal code cannot be left blank")
     * @Assert\Regex(pattern ="/[ABCEGHJKLMNPRSTVXY][0-9][ABCEGHJKLMNPRSTVWXYZ] [0-9][ABCEGHJKLMNPRSTVWXYZ][0-9]/", 
     * message = "A proper postal code must be entered in the form of L#L #L#")
     *
     *
     */
    private $postalCode;

    /**
     * @var string
     *
     * @ORM\Column(name="city", type="string", length=100)
     *
     * @Assert\NotBlank(message = "City cannot be left blank")
     *
     *  @Assert\Length(max=100 , maxMessage = "Length can't be more than 100 characters long.")
     */
    private $city;

    /**
     * @var string
     *
     * @ORM\Column(name="province", type="string", length=100)
     *
     * @Assert\NotBlank(message = "Province cannot be left blank")
     *
     * @Assert\Length(max=100 , maxMessage = "Length can't be more than 100 characters long.")
     */
    private $province;

    /**
     * @var string
     *
     * @ORM\Column(name="country", type="string", length=40)
     *
     * @Assert\NotBlank(message = "Country cannot be left blank")
     *
     * @Assert\Length(max=40 , maxMessage = "Length can't be more than 100 characters long.")
     */
    private $country;


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
     * Set streetAddress
     *
     * @param string $streetAddress
     *
     * @return Address
     */
    public function setStreetAddress($streetAddress)
    {
        $this->streetAddress = $streetAddress;

        return $this;
    }

    /**
     * Get streetAddress
     *
     * @return string
     */
    public function getStreetAddress()
    {
        return $this->streetAddress;
    }

    /**
     * Set postalCode
     *
     * @param string $postalCode
     *
     * @return Address
     */
    public function setPostalCode($postalCode)
    {
        $this->postalCode = $postalCode;

        return $this;
    }

    /**
     * Get postalCode
     *
     * @return string
     */
    public function getPostalCode()
    {
        return $this->postalCode;
    }

    /**
     * Set city
     *
     * @param string $city
     *
     * @return Address
     */
    public function setCity($city)
    {
        $this->city = $city;

        return $this;
    }

    /**
     * Get city
     *
     * @return string
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * Set province
     *
     * @param string $province
     *
     * @return Address
     */
    public function setProvince($province)
    {
        $this->province = $province;

        return $this;
    }

    /**
     * Get province
     *
     * @return string
     */
    public function getProvince()
    {
        return $this->province;
    }

    /**
     * Set country
     *
     * @param string $country
     *
     * @return Address
     */
    public function setCountry($country)
    {
        $this->country = $country;

        return $this;
    }

    /**
     * Get country
     *
     * @return string
     */
    public function getCountry()
    {
        return $this->country;
    }

    public function __toString()
    {
        return $this->streetAddress;
    }
}
