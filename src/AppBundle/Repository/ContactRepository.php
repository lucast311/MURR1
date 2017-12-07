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
        $contact->setRole($contact->getRole()); 
        // persist the new contact in the database
        $em->persist($contact);
        // get the address out of the contact and persist it too
        $em->persist($contact->getAddress());
        // flush them to the database
        $em->flush();
        //Close the entity manager
        // return the id of the new contact in the database
        return $contact->getId();
    }



    /**
     * This function will update a contact in the database.
     * ID is required to be set on the object.
     * @param Contact $contact The contact to update in the database.
     * @return integer The id of the object in the database.
     */
    public function update($id, Contact $contact)
    {

        $em = $this->getEntityManager();
        $oldContact = $em->getRepository(Contact::class)->find($id);

        $oldContact->setFirstName($contact->getFirstName());
        $oldContact->setLastName($contact->getLastName());
        $oldContact->setCompanyName($contact->getCompanyName());
        $oldContact->setPrimaryPhone($contact->getPrimaryPhone());
        $oldContact->setPhoneExtention($contact->getPhoneExtension());
        $oldContact->setSecondaryPhone($contact->getSecondaryPhone());
        $oldContact->setEmailAddress($contact->getEmailAddress());
        $oldContact->setFax($contact->getFax());
        $oldContact->setRole($contact->getRole());
        $oldContact->setAddress($contact->getAddress()); 
        $em->persist($oldContact); 
        $em->flush();

    }


    /**
     * Returns all the contacts out of the database sorted by first name, then last name
     * @return array array of contacts in the database.
     */
    public function getAll()
    {
        // Query the database and return an array of contacts
        return $this->getEntityManager()
            ->createQuery(
                'SELECT c FROM AppBundle:Contact c ORDER BY c.firstName, c.lastName ASC'
            )
            ->getResult();
    }

    /**
     * Returns a single contact from the databased based on the id specified.
     * @param mixed $contactId the id of the contact you wish to obtain.
     * @return mixed the contact that was found.
     */
    public function getOne($contactId)
    {
        // Use the built in repository to find the specific contact by id
        return $this->getEntityManager()->getRepository(Contact::class)->findOneById($contactId);
    }
}