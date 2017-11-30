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
            // a variable set to the passed in string after being trimmed
            $queryString = trim($searchQuery);

            //// Break apart the passed in string based on 'comma spaces'
            //if(strpos($queryString, ', '))
            //{
            //    $queries = explode(', ', $queryString);
            //}
            //// Break apart the passed in string based on 'comma's'
            //else if(strpos($queryString, ','))
            //{
            //    $queries = explode(',', $queryString);
            //}
            //// Break apart the passed in string based on 'spaces'
            //else
            //{
            //    $queries = explode(' ', $queryString);
            //}

            // get an entity manager
            $em = $this->getDoctrine()->getManager();

            //$tempQueries = array();
            //foreach($queries as $index=>$string)
            //{
            //    if($string != '')
            //    {
            //        $tempQueries[]=$queries[$index];
            //    }
            //}
            //$queries = $tempQueries;



            // Use the repository to query for the records we want.
            // Store those records into an array.
            $contactSearches = $em->getRepository(Contact::class)->contactSearch($queryString);


            // Break apart the passed in string based on 'comma spaces'
            if(strpos($queryString, ', '))
            {
                $queries = explode(', ', $queryString);
            }
            // Break apart the passed in string based on 'comma's'
            else if(strpos($queryString, ','))
            {
                $queries = explode(',', $queryString);
            }
            // Break apart the passed in string based on 'spaces'
            else
            {
                $queries = explode(' ', $queryString);
            }

            $tempQueries = array();
            foreach($queries as $index=>$string)
            {
                if($string != '')
                {
                    $tempQueries[]=$queries[$index];
                }
            }
            $queries = $tempQueries;


            //var_dump($contactSearches);

            // An open square bracket to denote the start of the JSON object string
            $jsonEncodedSearches = "[";

            // foreach record returned
            foreach ($contactSearches as $result)
            {
                // change the "new Contact()" to be a new instance of the object you are searching for
                $methods = get_class_methods(get_class(new Contact()));

                // an array to store the values of the returned objects
                $objectValues = array();

                // for each method in the entity you are searching for
                foreach ($methods as $method)
                {
                    // check if the method is a getter
                    if(strpos($method, 'get')===0)
                    {
                        // check if the method is for the id
                        if(strpos($method, 'getId')===0)
                        {
                            // call getId and store its value in the array created above
                            $objectValues[] = $result->getId();
                        }
                        // check if the method is for the Address (remove this "else if" if you do not have a join in your entity)
                        else if(strpos($method, 'getAddress')===0)
                        {
                            //$objectValues[] = $result['address']
                        }
                        else
                        {
                            // call the getter method and store the value returned
                            $objectValues[] = call_user_func([$result, $method]) == null ? 'null' : '"'.call_user_func([$result, $method]).'"';
                        }
                    }
                }

                // The second loop you see below is not needed unless the object you are searching for has a join

                // re-evaluate the methods in the object for the address object
                $methods = get_class_methods(get_class(new Address()));

                // foreach method in Address
                foreach ($methods as $method)
                {
                    // check if the method is a getter
                    if(strpos($method, 'get')===0)
                    {
                        // check if the method is for the id
                        if(strpos($method, 'getId')===0)
                        {
                            // call getId and store its value in the array created above
                            $objectValues[] = $result->getId();
                        }
                        else
                        {
                            // call the getter method and store the value returned
                            $objectValues[] = call_user_func([$result->getAddress(), $method]) == null ? 'null' : '"'.call_user_func([$result->getAddress(), $method]).'"';
                        }
                    }
                }

                //$curID = $result->getId();
                //$curFName = $result->getFirstName() == null ? 'null' : '"'.$result->getFirstName().'"';
                //$curLName = $result->getLastName() == null ? 'null' : '"'.$result->getLastName().'"';
                //$curOrg = $result->getOrganization() == null ? 'null' : '"'.$result->getOrganization().'"';
                //$curPPhone = $result->getprimaryPhone() == null ? 'null' : '"'.$result->getprimaryPhone().'"';
                //$curPExt = $result->getphoneExtension() == null ? 'null' : '"'.$result->getphoneExtension().' (might be int)[!DEBUG!INFO!]"';
                //$curSPhone = $result->getsecondaryPhone() == null ? 'null' : '"'.$result->getsecondaryPhone().'"';
                //$curEMail = $result->getEmailAddress() == null ? 'null' : '"'.$result->getEmailAddress().'"';
                //$curFax = $result->getFax() == null ? 'null' : '"'.$result->getFax().'"';
                //$curAddrId = $result->getAddress()->getId();
                //$curAddrStreet = $result->getAddress()->getStreetAddress() == null ? 'null' : '"'.$result->getAddress()->getStreetAddress().'"';
                //$curAddrPCode = $result->getAddress()->getPostalCode() == null ? 'null' : '"'.$result->getAddress()->getPostalCode().'"';
                //$curAddrCity = $result->getAddress()->getCity() == null ? 'null' : '"'.$result->getAddress()->getCity().'"';
                //$curAddrProv = $result->getAddress()->getProvince() == null ? 'null' : '"'.$result->getAddress()->getProvince().'"';
                //$curAddrCountry = $result->getAddress()->getCountry() == null ? 'null' : '"'.$result->getAddress()->getCountry().'"';

                // a variable to store the JSON formatted string
                $currData = '';

                // populate the JSON string with the values from the array of object values
                foreach($objectValues as $value)
                {
                    $currData .= $value;
                }

                // a variable that will store the number of records returned
                $found = 0;

                // foreach separate string to query on in the passed in string
                foreach ($queries as $query)
                {
                    // determine if the passed in data is contained within any of the records returned
                    //$dataFoundAt = strpos($currData, $query);

                    // if the data to search for exists in the current record
                    if(strpos($currData, $query) > 0)
                    {
                        // increment found
                        $found++;
                    }
                }

                //foreach($queries as $index=>$string)
                //{
                //    if($string == '')
                //    {
                //        // remove it from the array of query strings
                //        unset($queries[$index]);
                //    }
                //}

                // if the records returned match all of the passed in criteria to search for
                if($found == sizeof($queries))
                {
                    // Convert the records returned into JSON format.
                    // For other Entities (not Contact), change the indicies below
                    //  to be the indicies of each of your records values, Ex:
                    //  id should always be at index 0, but firstName might not be at 1.
                    $jsonEncodedSearches.='{"id":'.$objectValues[0]
                    .',"firstName":'.$objectValues[1]
                    .',"lastName":'.$objectValues[2]
                    .',"organization":'.$objectValues[3]
                    .',"primaryPhone":'.$objectValues[4]
                    .',"phoneExtension":'.$objectValues[5]
                    .',"secondaryPhone":'.$objectValues[6]
                    .',"emailAddress":'.$objectValues[7]
                    .',"fax":'.$objectValues[8].'},';

                    // This code was for appending a Contact object's related Address data to the end of itself.
                    //.',"streetAddress":'.$curAddrStreet
                    //.',"postalCode":'.$curAddrPCode
                    //.',"city":'.$curAddrCity
                    //.',"province":'.$curAddrProv
                    //.',"country":'.$curAddrCountry.'},';
                }
            }

            // chop off the last comma at the end of the JSON string
            $jsonEncodedSearches = substr($jsonEncodedSearches,0,strlen($jsonEncodedSearches)-1);

            // close the square bracket (this is the end of the JSON object string)
            $jsonEncodedSearches .= "]";

            // render the page passing to it the records returned from the query, after being converted to JSON format.
            return $this->render('contactsearch/raw.html.twig', array(
                'contactSearches' => $jsonEncodedSearches,
            ));
        }

        // Display an error for testing if string to search on in bigger then 100 characters
        return $this->render('contactsearch/raw.html.twig', array(
                'contactSearches' => "Query string was too long.",
            ));
    }
}
