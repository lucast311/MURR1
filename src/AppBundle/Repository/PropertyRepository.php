<?php
namespace AppBundle\Repository;

use Doctrine\ORM\EntityRepository;
use AppBundle\Entity\Property;


class PropertyRepository extends EntityRepository
{
    /**
     * STORY 4A
     * Takes a property and inserts it into the database. The property
     * should have an address inside of it. Doctrine will automatically
     * associate keys and turn the address into a foreign key.
     * @param Property $property the property to insert
     * @return mixed the id of the new inserted property
     * @throws UniqueConstraintViolationException if ID already exists
     */
    public function insert(Property $property)
    {
        // get the entity manager
        $em = $this->getEntityManager();
        // persist the new property in the database
        $em->persist($property);
        // get the address out of the property and persist it too
        $em->persist($property->getAddress());

        // flush them to the database
        $em->flush();

        //Close the entity manager
        $em->close();
        $em = null;
        // return the id of the new property in the database
        return $property->getId();
    }


}