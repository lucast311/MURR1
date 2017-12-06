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
    public function viewAction()
    {
        return $this->render('contact/listContacts.html.twig');
    }
}
