<?php

namespace AppBundle\Repository;

use Doctrine\ORM\EntityRepository;
use AppBundle\Entity\Communication;

/**
 * This class will be responsible with interacting with the database
 * in relation to Communication objects
 */
class CommunicationRepository extends EntityRepository
{
    /**
     * This method will insert a single communication object into the database and
     * return its ID
     * @param Communication $communication 
     * @return integer
     */
    public function insert(Communication $communication)
    {
        $em = $this->getEntityManager(); //get the entity manager to insert the communication
        $em->persist($communication); //prepare the object to be inserted (creates an ID)
        $em->flush(); //add to database

        $em->close(); //close communication
        $em = null; //set to null as a best practice

        return $communication->getId(); //return the set ID
    }
}