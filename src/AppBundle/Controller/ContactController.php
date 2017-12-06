<?php
namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Entity\Contact;
use AppBundle\Entity\Address;
use AppBundle\Form\ContactType;
use AppBundle\Services\Cleaner;
use AppBundle\Services\SearchNarrower;
use AppBundle\Services\Changer;

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
     * Lists all contactSearch entities.
     *
     * @Route("/contact/search/{searchQuery}", name="contact_search")
     * @Method("GET")
     */
    public function searchAction($searchQuery)
    {
        // if the string to query onn is less than or equal to 100 characters
        if(strlen($searchQuery) <= 100)
        {
            // create a cleaner to cleanse the search query
            $cleaner = new Cleaner();

            // cleanse the query
            $cleanQuery = $cleaner->cleanSearchQuery($searchQuery);

            var_dump($cleanQuery);

            // get an entity manager
            $em = $this->getDoctrine()->getManager();

            // Use the repository to query for the records we want.
            // Store those records into an array.
            $contactSearches = $em->getRepository(Contact::class)->contactSearch($cleanQuery);

            // create a SearchNarrower to narrow down our searches
            $searchNarrower = new SearchNarrower();

            // narrow down our searches, and store their values along side their field values
            $searchedData = $searchNarrower->narrowContacts($contactSearches, $cleanQuery);

            // look in the array of narrowed searches/values for the first element (this will be the array of narrowed searches)
            $narrowedResults = $searchedData[0];

            // create a Changer to convert the narrowed searches to JSON format
            $changer = new Changer();

            // An open square bracket to denote the start of the JSON object string
            $jsonEncodedSearches = "[";

            // a counter to index into the array of narrowed results's data
            $i = 0;

            // foreach record in the array of narrowed results
            foreach ($narrowedResults as $result)
            {
                // append the converted entity JSON string to the string we created above.
                // the '$searchedData[1][$i]' is indexing into the current records field values
                $jsonEncodedSearches .= $changer->ToJSON($result, $searchedData[1][$i]);

                // increment our counter
                $i++;
            }

            // chop off the last comma at the end of the JSON string
            $jsonEncodedSearches = substr($jsonEncodedSearches,0,strlen($jsonEncodedSearches)-1);

            // if the length of the JSON string is greater than 0
            if(strlen($jsonEncodedSearches) > 0)
            {
                // close the square bracket (this is the end of the JSON object string)
                $jsonEncodedSearches .= "]";

                // render the page passing to it the records returned from the query, after being converted to JSON format.
                return $this->render('contactsearch/contactJSONSearches.html.twig', array(
                    'contactSearches' => $jsonEncodedSearches,
                ));
            }
        }

        // Display a blank JSON object, the system will interpret this as nothing being returned
        return $this->render('contactsearch/contactJSONSearches.html.twig', array(
                'contactSearches' => '[{"role":null}]',
            ));
    }
}
