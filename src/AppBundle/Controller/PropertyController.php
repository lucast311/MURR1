<?php
namespace AppBundle\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Entity\Property;
use AppBundle\Entity\Address;
use AppBundle\Form\PropertyType;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Symfony\Component\Form\FormError;
use AppBundle\Repository\PropertyRepository;
use AppBundle\Services\Cleaner;
use AppBundle\Services\SearchNarrower;
use AppBundle\Services\Changer;

/**
 * Controller that contains methods for anything having to do with a property.
 */
class PropertyController extends Controller
{
    /**
     * Story 4a
     * Handles the adding of a property.
     * @param Request $request
     *
     * @Route("/property/add", name="property_add")
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
     * @Route("/property/edit/{propertyId}", name="property_edit")
     * @Route("/property/edit/")
     */
    public function editAction(Request $request, $propertyId = null)
    {
        $repo = $this->getDoctrine()->getEntityManager()->getRepository(Property::class);
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
     * @Route("/property/view/{propertyId}", name="property_view")
     * @Route("/property/view")
     * @Route("/property/view/")
     */
    public function viewAction($propertyId = 'not_specified')
    {
        // Get the entity manager
        $em = $this->getDoctrine()->getManager();
        // Get the specific property
        $property = $em->getRepository(Property::class)->findOneById($propertyId);

        // Render the html and pass in the property
        return $this->render('property/viewProperty.html.twig', array('property'=>$property,
            'propertyId'=>$propertyId,
            'editPath'=>$this->generateUrl("property_edit", array('propertyId'=>$propertyId))));
    }

    
    /**
     * Story 4d
     * Lists all propertySearch entities.
     * @param String $searchQuery
     *
     * @Route("/property/search/{searchQuery}", name="property_search")
     * @Method("GET")
     *//*
    public function searchAction($searchQuery)
    {
        //CODESTUB
    }
    */
}
