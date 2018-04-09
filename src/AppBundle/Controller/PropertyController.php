<?php
namespace AppBundle\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Entity\Property;
use AppBundle\Entity\Address;
use AppBundle\Entity\Contact;
use AppBundle\Entity\Communication;
use AppBundle\Entity\Container;
use AppBundle\Form\PropertyType;
use AppBundle\Form\CommunicationType;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Symfony\Component\Form\FormError;
use AppBundle\Repository\PropertyRepository;
use AppBundle\Services\Cleaner;
use AppBundle\Services\SearchNarrower;
use AppBundle\Services\RecentUpdatesHelper;
use AppBundle\Services\Changer;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Serializer;
use AppBundle\Form\PropertyAddContactType;
use Symfony\Component\Form\FormBuilder;

/**
 * Controller that contains methods for anything having to do with a property.
 */
class PropertyController extends Controller
{
    public $property;

    /**
     * Handles removing an association between a contact and a property
     * @param Request $request
     * @Route("/property/removecontactfromproperty", name="remove_contact_from_property")
     * @method({"POST","GET"})
     */
    public function removeContactAction(Request $request)
    {
        if($request->getMethod() == 'POST')
        {
            $em = $this->getDoctrine()->getManager();
            $propertyRepo = $em->getRepository(Property::class);

            $property = $propertyRepo->findOneBy(array('id'=>$request->request->get('property')));
            $contact = $em->getRepository(Contact::class)->findOneBy(array('id'=>$request->request->get('contact')));

            if($contact != null && $property != null)
            {
                if(in_array($contact, $property->getContacts()->toArray()))
                {
                    $contacts = $property->getContacts();
                    $contacts->removeElement($contact);
                    $property->setContacts($contacts);
                    $propertyRepo->save($property);

                    return $this->redirectToRoute("property_view", array("propertyId"=>$property->getId()));
                }
            }
        }
        return $this->redirectToRoute("property_search");
    }

    /**
     * story4f
     * Front end for searching for a property.
     *
     * I have no clue why but DO NOT MOVE THIS TO THE BOTTOM OF THE FILE... if you
     * do, the route breaks, I suspect it has something to do with the crud generated route
     * for viewing a property.
     *
     * @Route("/property/search", name="property_search")
     * @Method("GET")
     */
    public function searchAction(Request $request)
    {
        // Get if it is a popup or not
        $isPopup = ($request->query->get("isPopup")) == "true" ? true : false;
        // Render the twig with required data
        return $this->render('property/searchProperty.html.twig', array(
            'viewURL' => '/property/view/',
            'isPopup' => $isPopup
        ));
    }


    /**
     * Story 4a
     * Handles the adding of a property.
     * @param Request $request
     *
     * @Route("/property/new", name="property_add")
     */
    public function addAction(Request $request)
    {
        $showSuccess = false;
        // Create a new property
        $property = new Property();
        // Populate some common fields for convenience
        $property->setAddress(new Address());
        $property->getAddress()->setCity("Saskatoon");
        $property->getAddress()->setProvince("Saskatchewan");
        $property->getAddress()->setCountry("Canada");

        // Create the form
        $form = $this->createForm(PropertyType::class, $property);

        // handle the submitted data coming in (if there was any).
        $form->handleRequest($request);

        // if the form is submitted and is valid
        if ($form->isSubmitted() && $form->isValid())
        {
        	// Insert the new data into the database
            //the value for type could be a blank string so change it to null for the database
            if($property->getPropertyType() == '') $property->setPropertyType(null);

            $this->getDoctrine()->getRepository(Property::class)->save($property);
            // Nuke the form and contact so that on a successful submit the form fields are now blank
            $property = new Property();
            $property->setAddress(new Address());
            $property->getAddress()->setCity("Saskatoon");
            $property->getAddress()->setProvince("Saskatchewan");
            $property->getAddress()->setCountry("Canada");

            $form = $this->createForm(PropertyType::class, $property);
            // Show the success message
            $showSuccess = true;
        }

        // Render the form
        return $this->render('property/addProperty.html.twig',
            array('form'=>$form->createView(), 'showSuccess'=>$showSuccess));
    }

    /**
     * Story 4c User Edits Property
     *
     * This function will be responsible for handling the requests to the edit page
     * and contain the form to edit a property
     * @param mixed $propertyId
     * @Route("/property/{propertyId}/edit", name="property_edit")
     */
    public function editAction(Request $request, $propertyId = null)
    {
        $repo = $this->getDoctrine()->getManager()->getRepository(Property::class);
        $property = $repo->findOneById($propertyId);

        $errorType = null;

        if($property == null) $errorType="notfound";
        if($propertyId == null) $errorType="noid";

        $form = $this->createForm(PropertyType::class, $property);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            //If the property type is not selected, make it null
            if($property->getPropertyType() == '') $property->setPropertyType(null);
            $repo->save($property);
            return $this->redirectToRoute('property_view', array('propertyId'=>$propertyId),301);
        }

        return $this->render('property/editProperty.html.twig',
            array('form'=>$form->createView(), 'errorType'=>$errorType,
            'cancelPath' => $this->generateUrl('property_view', array('propertyId'=>$propertyId))));
    }

    /**
     * Story 4a
     * Handles the viewing of a property.
     * @param Request $request
     *
     * @Route("/property/{propertyId}", name="property_view")
     * @Route("/property")
     * @Route("/property/")
     * @Method({"GET","POST"})
     */
    public function viewAction($propertyId = 'not_specified', $addCommunicationForm = null, Request $request)
    {

        //Default don't dhow the communication form
        $showCommunicationForm = false;
        //sets the property name to be an empty string instead of using the actual property
        //this is so the twig doesn't throw an error if the property isn't valid 
        $propertyName = "";

        //if the form wasn't given to us, create it
        if($addCommunicationForm == null)
        {
            // Get the communication form to pass it in
            $addCommunicationForm = $this->createForm(CommunicationType::class, new Communication());
        }
        //if the form was submitted and isn't valid, set the modal to popup
        else if($addCommunicationForm->isSubmitted() && !$addCommunicationForm->isValid())
        {
            $showCommunicationForm = true;
        }


        // Get the entity manager
        $em = $this->getDoctrine()->getManager();
        // Get the specific property
        $property = $em->getRepository(Property::class)->findOneById($propertyId);

        //if the property isn't null, do all the actions required
        if($property != null)
        {
            $addContactForm = $this->createForm(PropertyAddContactType::class, null,array('property'=>$property->getId()));
            $deleteForm = $this->createDeleteForm($property);
            $propertyName = $property->getPropertyName();
        } else {
            //if not, set the forms to null and the property to false
            $property = false;
            $deleteForm = null;
            $addContactForm = null;
        }

        //now check if the method is post and property is not null
        if($request->getMethod() == 'POST' && $property != null)
        {
            if($request->request->has('appbundle_contactToProperty'))
            {
                $em = $this->getDoctrine()->getManager();
                $contactRepo = $em->getRepository(Contact::class);

                $contact = $contactRepo->findOneById($request->request->get('appbundle_contactToProperty')['contact']);
                if($property->getContacts()->contains($contact))
                {
                    $addContactForm->addError(new FormError('This property is already associated to the selected contact'));
                }
                else
                {
                    $contacts = $property->getContacts();
                    $contacts->add($contact);
                    $property->setContacts($contacts);
                    $em->getRepository(Property::class)->save($property);
                    $em->refresh($property);
                }
            }

        }
        // Render the html and pass in the property
        //if the property is false, send everything but the form data 
        if($property == false)
        {
            return $this->render('property/viewProperty.html.twig', array(
                'property'=>$property,
                'propertyId'=>$propertyId,
                'propertyName'=>$propertyName,
                'editPath'=>$this->generateUrl("property_edit", array('propertyId'=>$propertyId)),
                'newCommunicationForm' => $addCommunicationForm->createView(),
                'showCommunicationForm' => $showCommunicationForm)); 
        } else {
            //else return it with the correct data to display 
            return $this->render('property/viewProperty.html.twig', array(
                'property'=>$property,
                'propertyId'=>$propertyId,
                'propertyName'=>$propertyName,
                'delete_form' => $deleteForm->createView(),
                'editPath'=>$this->generateUrl("property_edit", array('propertyId'=>$propertyId)),
                'newCommunicationForm' => $addCommunicationForm->createView(),
                'showCommunicationForm' => $showCommunicationForm,
                'add_contact_form' => $addContactForm->createView()));
        }

    }

    /**
     * Story 4d
     * Lists all propertySearch entities.
     *
     * @Route("/property/jsonsearch/", name="property_jsonsearch_empty")
     * @Route("/property/jsonsearch/{searchQuery}", name="property_jsonsearch")
     * @Method("GET")
     */
    public function jsonSearchAction($searchQuery = "")
    {
        if($searchQuery != "")
        {
            // Clean the input
            $searchQuery = htmlentities($searchQuery);

            // if the string to query onn is less than or equal to 100 characters
            if(strlen($searchQuery) <= 100 && !empty($searchQuery))
            {
                // create a cleaner to cleanse the search query
                $cleaner = new Cleaner();

                // cleanse the query
                $cleanQuery = $cleaner->cleanSearchQuery($searchQuery);

                // get an entity manager
                $em = $this->getDoctrine()->getManager();

                // Use the repository to query for the records we want.
                // Store those records into an array.
                $propertySearches = $em->getRepository(Property::class)->propertySearch($cleanQuery);

                // create a SearchNarrower to narrow down our searches
                $searchNarrower = new SearchNarrower();

                // narrow down our searches, and store their values along side their field values
                $searchedData = $searchNarrower->narrower($propertySearches, $cleanQuery, new Property());

                // look in the array of narrowed searches/values for the first element (this will be the array of narrowed searches)
                //$narrowedResults = $searchedData[0];

                $encoder = new JsonEncoder();
                $normalizer = new ObjectNormalizer();
                $normalizer->setIgnoredAttributes(array("contacts", "bins", "buildings","communications",
                    "__initializer__", "__cloner__", "__isInitialized__")); //idk why i need these ones, but I do..
                $serializer = new Serializer(array($normalizer), array($encoder));


                // Return the results as a json object
                // NOTE: Serializer service needs to be enabled for this to work properly
                return JsonResponse::fromJsonString($serializer->serialize($searchedData, 'json'));
            }
        }
        else
        {
            // get an entity manager
            $em = $this->getDoctrine()->getManager();

            // get the RecentUpdates service to query for the 10 most recently updated properties
            $recentUpdates = new RecentUpdatesHelper();

            // the service takes in an EntityManager, and the name of the Entity
            $tenRecent = $recentUpdates->tenMostRecent($em, 'AppBundle:Property');

            // Return the results as a json object
            // NOTE: Serializer service needs to be enabled for this to work properly
            $encoder = new JsonEncoder();
            $normalizer = new ObjectNormalizer();

            //// We used to get a circular reference error. This line prevents it.
            //$normalizer->setCircularReferenceHandler(function($object){return $object->getDate();});

            // Don't display the 'contacts', 'bins', 'buildings', 'communications', 'dateModified', or Joins data as JSON.
            // Makes it more human readable.
            $normalizer->setIgnoredAttributes(array("contacts", "bins", "buildings","communications", "dateModified",
                "__initializer__", "__cloner__", "__isInitialized__")); //idk why i need these ones, but I do..
            $serializer = new Serializer(array($normalizer), array($encoder));

            return JsonResponse::fromJsonString($serializer->serialize($tenRecent, 'json'));
        }

        // string over 100, return empty array.
        return $this->json(array());
    }

    /**
     * Delete a property entity
     * @param Request $request
     * @param Property $property
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     *
     * @Route("/property/{id}", name="property_delete")
     * @method("DELETE")
     */
    public function deleteAction(Request $request, Property $property)
    {
        $form = $this->createDeleteForm($property);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            $em = $this->getDoctrine()->getManager();
            $em->remove($property);
            $em->flush();
        }

        return $this->redirectToRoute('property_search');
    }

    /**
     * Summary of createDeleteForm
     * @param Property $property
     * @return mixed
     */
    private function createDeleteForm(Property $property)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('property_delete', array('id' => $property->getId())))
            ->setMethod('DELETE')
            ->getForm();
    }


}
