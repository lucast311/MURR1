<?php

namespace AppBundle\Repository;

use Doctrine\ORM\EntityRepository;
use AppBundle\Entity\Structure; 

/**
 * StructureRepository short summary.
 *
 * StructureRepository description.
 *
 * @version 1.0
 * @author cst201
 */
class StructureRepository extends EntityRepository
{
    public function save(Structure $structure)
    {
        $em = $this->getEntityManager();
        // persist the new contact in the database
        $em->persist($structure);
        // flush them to the database
        $em->flush();
        //Close the entity manager
        // return the id of the new contact in the database
        return $structure->getId();
    }
}