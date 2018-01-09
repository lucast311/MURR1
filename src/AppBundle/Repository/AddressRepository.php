<?php
namespace AppBundle\Repository;

use Doctrine\ORM\EntityRepository;
use AppBundle\Entity\Address;

/**
 * Handles querying the database for information related to an address.
 */
class AddressRepository extends EntityRepository
{

    public function save(Address $address)
    {
        $em = $this->getEntityManager();
        $em->persist($address);
        $em->flush();

        return $address->getId();
    }

    public function getOne($address)
    {
        // Use the built in repository to find the specific contact by id
        return $this->getEntityManager()->getRepository(Address::class)->findOneById($address->getId());
    }

}