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
        // Break apart the passed in string based on 'comma spaces'
        if(strpos($queryStrings, ', '))
        {
            $queryStrings = explode(', ', $queryStrings);
        }
        // Break apart the passed in string based on 'comma's'
        else if(strpos($queryStrings, ','))
        {
            $queryStrings = explode(',', $queryStrings);
        }
        // Break apart the passed in string based on 'spaces'
        else
        {
            $queryStrings = explode(' ', $queryStrings);
        }

        var_dump($queryStrings);

        // get the field names of both the Contact and Address Entities.
        $contactClassProperties = $this->getClassMetadata('AppBundle:Contact')->fieldNames;
        $addressClassProperties = $this->getEntityManager()->getRepository('AppBundle:Address')->getClassMetadata()->fieldNames;

        // shift off the id from the Contact (A user will never search based on this)
        array_shift($contactClassProperties);

        // a variable to store the SQLite WHERE clause to query with
        $searchString = '';

        $tempQueries = array();
        foreach($queryStrings as $index=>$string)
        {
            if($string != '')
            {
                $tempQueries[]=$queryStrings[$index];
            }
        }
        $queryStrings = $tempQueries;

        //foreach field in the Contact
        foreach($contactClassProperties as $col=>$val)
        {
            // foreach string to query on
            for ($i = 0; $i < sizeof($queryStrings); $i++)
            {
                // if the string to query on is a space
                if($queryStrings[$i] == '')
                {
                    // remove it from the array of query strings
                    unset($queryStrings[$i]);
                }
                else
                {
                    //otherwise append to the WHERE clause
                    $searchString .= "c.$val LIKE '%$queryStrings[$i]%' OR ";
                }
            }
        }

        // foreach field in the Address
        foreach($addressClassProperties as $col=>$val)
        {
            // foreach string to query on
            for ($i = 0; $i < sizeof($queryStrings); $i++)
            {
                // if the string to query on is a space
                if($queryStrings[$i] == '')
                {
                    // remove it from the array of query strings
                    unset($queryStrings[$i]);
                }
                else
                {
                    // otherwise append to teh WHERE clause
                    $searchString .= "a.$val LIKE '%$queryStrings[$i]%' OR ";
                }
            }
        }

        // Remove the unneeded ' OR ' from the end of the query string
        $searchString = rtrim($searchString, ' OR ');

        var_dump($queryStrings);
        var_dump($searchString);

        // return the records that were queried to the Controller
        //$return =  $this->getEntityManager()->createQuery(
        //   "SELECT c, a FROM AppBundle:Contact c JOIN c.address a"
        //    )->getResult();

        $return = $this->getEntityManager()->createQuery(
           "SELECT c, a FROM AppBundle:Contact c JOIN c.address a WHERE $searchString"
            )->getResult();

        var_dump($return);

        return $return;
    }
}
