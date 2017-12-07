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

}