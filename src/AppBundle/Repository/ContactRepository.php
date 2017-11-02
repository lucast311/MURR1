<?php
namespace AppBundle\Repository;

use Doctrine\ORM\EntityRepository;
use AppBundle\Entity\Contact;

/**
 * Handles querying the database for information related to contacts
 */
class ContactRepository extends EntityRepository
{
    /**
     * Takes a contact and inserts it into the database. The contact
     * should have an address inside of it. Doctrine will automatically
     * associate keys and turn the address into a foreign key.
     * @param Contact $contact
     * @return integer
     */
    public function insert(Contact $contact)
    {
        // get the entity manager
        $em = $this->getEntityManager();
        // persist the new contact in the database
        $em->persist($contact);
        // get the address out of the contact and persist it too
        $em->persist($contact->getAddress());
        // flush them to the database
        $em->flush();
        //Close the entity manager
        $em->close();
        // return the id of the new contact in the database
        return $contact->getId();
    }

    public function getAll()
    {
        
    }

    public function getOne()
    {
        
    }


}