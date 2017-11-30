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

            $this->getDoctrine()->getRepository(Property::class)->insert($property);
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

    public function editAction($propertyId = -1)
    {
        
    }
}
