<?php
namespace AppBundle\DataFixtures\ORM;

use AppBundle\Entity\User;
use AppBundle\DataFixtures\ORM\LoadAddressData;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class LoadUserData implements FixtureInterface
{
    /**
     * Story_15a
     * @param ObjectManager $obMan the object manager
     */
    public function load(ObjectManager $obMan, UserPasswordEncoderInterface $encoder)
    {
        $user = new User();
        $user->setUsername("admin");
        // Make sure you encode the password
        $user->setPassword($encoder->encodePassword($user, "password"));
        $user->setRole("ADMIN");

        // add the Property to the database
        $obMan->persist($user);

        // flush the database connection
        $obMan->flush();
    }
}