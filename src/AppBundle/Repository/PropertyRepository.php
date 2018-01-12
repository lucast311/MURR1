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
    public function save(Property $property)
    {
        // get the entity manager
        $em = $this->getEntityManager();
        // persist the new property in the database
        $em->persist($property);
        // get the address out of the property and persist it too
        $em->persist($property->getAddress());

        // flush them to the database
        $em->flush();

        //DO NOT CLOSE THE ENTITY MANAGER
        //The repo will not regenerate a new one
        //Close the entity manager
        //$em->close();
        //$em = null;
        // return the id of the new property in the database
        return $property->getId();
    }

    /**
     * STORY 4h
     * Takes an integer property id and returns an array of all containers associated
     * with that property. The property id should be greater than 0.
     * @param Property $property the property to find containers for
     * @return mixed the array of containers associated with that property
     */
    public function getAllContainers(int $propertyId)
    {

    }

}