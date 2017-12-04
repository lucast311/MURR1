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

    public function contactSearch($queryStrings)
    {
        // get the field names of both the Contact and Address Entities.
        $contactClassProperties = $this->getClassMetadata('AppBundle:Contact')->fieldNames;
        $addressClassProperties = $this->getEntityManager()->getRepository('AppBundle:Address')->getClassMetadata()->fieldNames;

        // shift off the id from the Contact (A user will never search based on this)
        array_shift($contactClassProperties);

        // a variable to store the SQLite WHERE clause to query with
        $searchStringContacts = '';
        $searchStringAddresses = '';

        //foreach field in the Contact
        foreach($contactClassProperties as $col=>$val)
        {
            // foreach string to query on
            for ($i = 0; $i < sizeof($queryStrings); $i++)
            {
                //otherwise append to the WHERE clause
                $searchStringContacts .= "c.$val LIKE '%$queryStrings[$i]%' OR ";
            }
        }

        $searchStringContacts = rtrim($searchStringContacts, ' OR ');

        //foreach field in the Address
        foreach($addressClassProperties as $col=>$val)
        {
            // foreach string to query on
            for ($i = 0; $i < sizeof($queryStrings); $i++)
            {
                // otherwise append to teh WHERE clause
                $searchStringAddresses .= "a.$val LIKE '%$queryStrings[$i]%' OR ";
            }
        }

        // Remove the unneeded ' OR ' from the end of the query string
        $searchStringAddresses = rtrim($searchStringAddresses, ' OR ');





        // return the records that were queried to the Controller






        $returnContacts = $this->getEntityManager()->createQuery(
           "SELECT c FROM AppBundle:Contact c WHERE $searchStringContacts"
            )->getResult();

        $returnAddresses = $this->getEntityManager()->createQuery(
           "SELECT c, a FROM AppBundle:Contact c JOIN c.address a WHERE $searchStringAddresses"
            )->getResult();


        foreach($returnAddresses as $returnAddress)
        {
            if(!in_array($returnAddress,$returnContacts))
            {
                $returnContacts[] = $returnAddress;
            }
        }

        return $returnContacts;
    }
}
