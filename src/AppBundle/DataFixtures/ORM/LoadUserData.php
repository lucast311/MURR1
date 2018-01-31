<?php
namespace AppBundle\DataFixtures\ORM;

use AppBundle\Entity\User;
use AppBundle\DataFixtures\ORM\LoadAddressData;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class LoadUserData implements FixtureInterface
{
    private $encoder;
    //dependency injection because load wont inject a UserPasswordEncoderInterface automatically
    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }

    /**
     * Story_15a
     * @param ObjectManager $obMan the object manager
     */
    public function load(ObjectManager $obMan)//UserPasswordEncoderInterface $encoder)
    {
        $user = new User();
        $user->setUsername("admin");

        // Make sure you encode the password
        $user->setPassword($this->encoder->encodePassword($user, "password"));
        $user->setRole("ROLE_ADMIN");

        // add the Property to the database
        $obMan->persist($user);

        // flush the database connection
        $obMan->flush();
    }
}