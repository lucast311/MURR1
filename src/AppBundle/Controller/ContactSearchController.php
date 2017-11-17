<?php

namespace AppBundle\Controller;

use AppBundle\Entity\ContactSearch;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

/**
 * Contactsearch controller.
 *
 * @Route("contactsearch")
 */
class ContactSearchController extends Controller
{
    /**
     * Lists all contactSearch entities.
     *
     * @Route("/", name="contactsearch_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $contactSearches = $em->getRepository('AppBundle:ContactSearch')->findAll();

        return $this->render('contactsearch/index.html.twig', array(
            'contactSearches' => $contactSearches,
        ));
    }

    /**
     * Finds and displays a contactSearch entity.
     *
     * @Route("/{id}", name="contactsearch_show")
     * @Method("GET")
     */
    public function showAction(ContactSearch $contactSearch)
    {

        return $this->render('contactsearch/show.html.twig', array(
            'contactSearch' => $contactSearch,
        ));
    }
}
