<?php

namespace AppBundle\Repository;

use Doctrine\ORM\EntityRepository;
use AppBundle\Entity\Container;

/**
 * ContainerRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class ContainerRepository extends EntityRepository
{
    public function save(Container $container)
    {
        $em = $this->getEntityManager();
        // persist the new contact in the database
        $em->persist($container);
        // flush them to the database
        $em->flush();
        //Close the entity manager
        // return the id of the new contact in the database
        return $container->getId();
    }

}