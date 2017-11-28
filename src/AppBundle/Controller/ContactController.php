<?php
namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Entity\Contact;
use AppBundle\Entity\Address;
use AppBundle\Form\ContactType;

/**
 * Controller that contains methods for anything having to do with a contact.
 */
class ContactController extends Controller
{
    /**
     * Handles the adding of a contact.
     * @param Request $request
     *
     * @Route("/contact/add", name="contact_add")
     */
    public function addAction(Request $request)
    {
        $showSuccess = false;
        // Create a new contact
        $contact = new Contact();
        // Populate some common fields for convenience
        $contact->setAddress(new Address());
        $contact->getAddress()->setCity("Saskatoon");
        $contact->getAddress()->setProvince("Saskatchewan");
        $contact->getAddress()->setCountry("Canada");

        // Create the form
        $form = $this->createForm(ContactType::class, $contact);

        // handle the submitted data coming in (if there was any).
        $form->handleRequest($request);

        // if the form is submitted and is valid
        if ($form->isSubmitted() && $form->isValid())
        {
        	// Insert the new data into the database
            $em = $this->getDoctrine()->getManager();
            $em->getRepository(Contact::class)->insert($contact);
            // Nuke the form and contact so that on a successful submit the form fields are now blank
            $contact = new Contact();
            $contact->setAddress(new Address());
            $contact->getAddress()->setCity("Saskatoon");
            $contact->getAddress()->setProvince("Saskatchewan");
            $contact->getAddress()->setCountry("Canada");

            $form = $this->createForm(ContactType::class, $contact);
            // Show the success message
            $showSuccess = true;
        }


        // Render the form
        return $this->render('contact/addContact.html.twig',
            array('form'=>$form->createView(), 'showSuccess'=>$showSuccess));
    }


    /**
     * Handles displaying all the contacts to the page
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @Route("/contact/list", name="contact_list")
     */
    public function listAction()
    {
        // Get the entity manager
        $em = $this->getDoctrine()->getManager();
        // Get all the contacts
        $contacts = $em->getRepository(Contact::class)->getAll();

        // Render the html and pass it a list of all contacts
        return $this->render('contact/listContacts.html.twig', array('contacts'=>$contacts));
    }
    /**
     * Handles the viewing of a specific contact with all of its details
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @Route("/contact/view/{contactId}", name="contact_view")
     */
    public function viewAction($contactId = -1)
    {
        // Get the entity manager
        $em = $this->getDoctrine()->getManager();
        // Get the specific contact
        $contact = $em->getRepository(Contact::class)->getOne($contactId);

        // Render the html and pass in the contact
        return $this->render('contact/viewContact.html.twig', array('contact'=>$contact));
    }

    /**
     * Handles the editing of a specific contact with all of its details
     * @return \Symfony\Component\HttpFoundation\Response
     * 
     * @route("/contact/view/{contactId}/edit", name="contact_Edit")
     */
    public function editAction()
    {
        return $this->render('contact/editContact.html.twig', array('id'=>1));
    }
}
