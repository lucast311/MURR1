<?php
namespace AppBundle\Repository;

use Doctrine\ORM\EntityRepository;
use AppBundle\Entity\Address;

/**
 * Handles querying the database for information related to an address.
 */
class AddressRepository extends EntityRepository
{
    /**
     * This function will insert a new address into the address table
     * in the database. ID is not required to be set on the object,
     * as doctrine will automatically generate it.
     * @param Address $address The address to insert into the database.
     * @return integer The id of the new object in the database.
     */
    public function insert(Address $address)
    {
        // Get the entity manager
        $em = $this->getEntityManager();
        // tell the entity manager to persist the object
        $em->persist($address);
        // flush the information into the database
        $em->flush();

        // close the entity manager
        $em->close();
        // return the id of the new entry in the database
        return $address->getId();
    }

    public function update(int $id)
    {

    }

}