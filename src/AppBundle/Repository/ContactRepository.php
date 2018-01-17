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
     * does the insert and update of contacts
     * @param Contact $contact - the contact to add to the db 
     * @return integer
     */
    public function save(Contact $contact)
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

	/**
     * Search through the database and check if any records contain any of
     *  the passed in strings (array of strings) in any of their fields.
     * @param mixed $queryStrings an array of strings to query the database on
     * @return array of searched entites returned from the queries
     */
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
                // otherwise append to the WHERE clause while checking on lower case (this makes the search case insensitive)
                $searchStringContacts .= "LOWER(c.$val) LIKE '%{$queryStrings[$i]}%' OR ";
            }
        }

        // Remove the unneeded ' OR ' from the end of the query string
        $searchStringContacts = rtrim($searchStringContacts, ' OR ');

        //foreach field in the Address
        foreach($addressClassProperties as $col=>$val)
        {
            // foreach string to query on
            for ($i = 0; $i < sizeof($queryStrings); $i++)
            {
                // otherwise append to the WHERE clause while checking on lower case (this makes the search case insensitive)
                $searchStringAddresses .= "LOWER(a.$val) LIKE '%{$queryStrings[$i]}%' OR ";
            }
        }

        // Remove the unneeded ' OR ' from the end of the query string
        $searchStringAddresses = rtrim($searchStringAddresses, ' OR ');

        // set variables equal to the records returned from each of the two queries
        $returnContacts = $this->getEntityManager()->createQuery(
           "SELECT c FROM AppBundle:Contact c WHERE $searchStringContacts"
            )->getResult();

        $returnAddresses = $this->getEntityManager()->createQuery(
           "SELECT c, a FROM AppBundle:Contact c JOIN c.address a WHERE $searchStringAddresses"
            )->getResult();

        // foreach address returned
        foreach($returnAddresses as $returnAddress)
        {
            // check if the address already exists in the array of contacts returned
            if(!in_array($returnAddress,$returnContacts))
            {
                // combine the search results
                $returnContacts[] = $returnAddress;
            }
        }

        // return the results
        return $returnContacts;
    }
}
