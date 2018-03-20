<?php
namespace AppBundle\Repository;

use Doctrine\ORM\EntityRepository;
use AppBundle\Entity\Contact;
use AppBundle\Services\SearchHelper;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;

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
        //// get the address out of the contact and persist it too
        //$em->persist($contact->getAddress());


        //try
        //{
            // flush them to the database
            $em->flush();
        //}
        //catch (UniqueConstraintViolationException $e)
        //{

        //}

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

        //Add all of the class properties arrays to one array
        $classPropertiesArray = array($contactClassProperties, $addressClassProperties);

        $count = 0;
        // shift off the id of each entity
        foreach ($classPropertiesArray as $array)
        {
            array_shift($array);
            $classPropertiesArray[$count] = $array;
            $count++;
        }

        //an array of abbreviations to be used in the query. These represent each join
        $classNames = array('co', 'a');

        //create a searchHelper instance
        $searchHelper = new SearchHelper();

        //call the searchHelper service to return the class properties string
        $classPropertiesString = $searchHelper->searchHelper($classPropertiesArray, $queryStrings, $classNames);

        // The query that defines all the joins on communications to search for,
        //  and links them together based on id's
        $records = $this->getEntityManager()->createQuery(
        "SELECT co, a FROM AppBundle:Contact co
        LEFT OUTER JOIN AppBundle:Address a WITH co.address = a.id
        WHERE $classPropertiesString"
        )->getResult();

        // remove any NULL values from the array (NULL values are represented by non-contact objects)
        $records = array_filter($records);

        $contactObjects = array();

        foreach ($records as $record)
        {
            if(get_class($record) == "AppBundle\Entity\Contact")
            {
                $contactObjects[] = $record;
            }
        }

        return $contactObjects;
    }

}
