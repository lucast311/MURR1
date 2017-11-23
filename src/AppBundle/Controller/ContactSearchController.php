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

        return $this->render('contactsearch/raw.html.twig', array(
            'contactSearches' => $contactSearches,
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
