<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Contact;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

/**
 * Contactsearch controller.
*/
class ContactSearchController extends Controller
{
    /**
     * Lists all contactSearch entities.
     *
     * @Route("/contact/search/{searchQuery}", name="contact_search")
     * @Method("GET")
     */
    public function searchAction($searchQuery)
    {
        $em = $this->getDoctrine()->getManager();

        $contactSearches = $em->getRepository('AppBundle:Contact')->contactSearch($searchQuery);

        $jsonEncodedSearches = "[";
        foreach ($contactSearches as $result)
        {
            $curID = $result->getId();
            $curFName = $result->getFirstName() == null ? 'null' : '"'.$result->getFirstName().'"';
            $curLName = $result->getLastName() == null ? 'null' : '"'.$result->getLastName().'"';
            $curOrg = $result->getOrganization() == null ? 'null' : '"'.$result->getOrganization().'"';
            $curPPhone = $result->getprimaryPhone() == null ? 'null' : '"'.$result->getprimaryPhone().'"';
            $curPExt = $result->getPhoneExtention() == null ? 'null' : '"'.$result->getPhoneExtention().' (might be int)[!DEBUG!INFO!]"';
            $curSPhone = $result->getsecondaryPhone() == null ? 'null' : '"'.$result->getsecondaryPhone().'"';
            $curEMail = $result->getEmailAddress() == null ? 'null' : '"'.$result->getEmailAddress().'"';
            $curFax = $result->getFax() == null ? 'null' : '"'.$result->getFax().'"';
            //THIS WILL BE ADDED BACK WHEN THE CONTACT ENTITY ISNT GARBAGE
            //$curAddress = $result->getAddress() == null ? 'null' : '"'.$result->getAddress().'"';

        	$jsonEncodedSearches.='{"id":'.$curID
                .',"firstName":'.$curFName
                .',"lastName":'.$curLName
                .',"organization":'.$curOrg
                .',"primaryPhone":'.$curPPhone
                .',"phoneExtention":'.$curPExt
                .',"secondaryPhone":'.$curSPhone
                .',"emailAddress":'.$curEMail
                .',"fax":'.$curFax.'},';//.
                //null,"address": THIS WILL BE ADDED BACK WHEN THE CONTACT ENTITY ISNT GARBAGE
                //{"__initializer__":{},"__cloner__":{},"__isInitialized__":false}},
        }
        $jsonEncodedSearches = substr($jsonEncodedSearches,0,strlen($jsonEncodedSearches)-1);

        $jsonEncodedSearches .= "]";


        //$jsonEncodedSearches= json_encode($contactSearches);

        return $this->render('contactsearch/raw.html.twig', array(
            'contactSearches' => $jsonEncodedSearches,
        ));
    }

    ///**
    // * Finds and displays a contactSearch entity.
    // *
    // * @Route("/{id}", name="contactsearch_show")
    // * @Method("GET")
    // */
    //public function showAction(ContactSearch $contactSearch)
    //{

    //    return $this->render('contactsearch/show.html.twig', array(
    //        'contactSearch' => $contactSearch,
    //    ));
    //}
}
