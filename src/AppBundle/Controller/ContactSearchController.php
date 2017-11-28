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
    public function searchAction($searchQuery = '1')
    {
        $em = $this->getDoctrine()->getManager();

        $queryString = trim($searchQuery);

        $contactSearches = $em->getRepository(Contact::class)->contactSearch($queryString);

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

            $narrow = $curID . $curFName . $curLName . $curOrg . $curPPhone . $curPExt . $curSPhone . $curEMail . $curFax;


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
}