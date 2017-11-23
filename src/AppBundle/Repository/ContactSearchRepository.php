<?php
namespace AppBundle\Repository;

use Doctrine\ORM\EntityRepository;
use AppBundle\Entity\Contact;

/**
 * Handles querying the database for information related to contacts
 */
class ContactSearchRepository extends ContactRepository
{
    //deprecated/moved into ContactRepository
    //function contactSearch($queryString)
    //{
    //    $queryString = 'test, cat';

    //    $type = '';

    //    if(strpos($queryString, ', '))
    //    {
    //        $testStrings = explode(', ', $queryString);
    //    }
    //    else if(strpos($queryString, ','))
    //    {
    //        $testStrings = explode(',', $queryString);
    //    }
    //    else
    //    {
    //        // this means that the string is separated by spaces
    //        $testStrings = explode(' ', $queryString);
    //    }

    //    $classProperties = get_class_vars(get_class(new Contact()));

    //    $searchString = '';
    //    foreach($classProperties as $col=>$val)
    //    {
    //        for($j = 0; $j < sizeof($testStrings); $j++)
    //        {
    //            $classProperties .= "$col LIKE '%$testStrings[$j]%' OR ";
    //        }
    //    }
    //    $searchString = rtrim($searchString, ' OR ');

    //    //$stmt = $db->prepare('SELECT * FROM Pet WHERE name IN (\'Fido\')');
    //    //$stmt = $db->prepare(sprintf('SELECT * FROM Pet WHERE name %s OR gender %s', $stringValues, $stringValues));

    //    return $this->getEntityManager()->createQuery(
    //        sprintf('SELECT * FROM Pet WHERE %s', $searchString)
    //        )->getResult();
    //}
}