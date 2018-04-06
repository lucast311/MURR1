<?php
namespace AppBundle\Controller;

use AppBundle\Entity\Truck;
use AppBundle\Form\TruckType;
use AppBundle\Services\Cleaner;
use AppBundle\Services\SearchNarrower;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Serializer;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

/**
 * Truck controller.
 *
 * @Route("truck")
 */
class TruckController extends Controller
{
    /**
     * STORY40A
     * Lists all truck entities.
     *
     * @Route("/", name="truck_util")
     * @param Request $request
     */
    public function indexAction(Request $request)
    {
        // Get the entity manager so we can interact with trucks in the DB
        $em = $this->getDoctrine()->getManager();

        $formTruck = new Truck();

        // Adding a new truck
        // Create a Truck form so the user can add trucks on the index page
        $addform = $this->createForm(TruckType::class, $formTruck);
        $addform->handleRequest($request);

        $showSuccess = false;

        // If the user has entered valid information and clicked the "Add" button
        if ($addform->isSubmitted() && $addform->isValid())
        {
            // Get the ID that the user has set and pad it if it isn't 6 characters already
            $formTruck->setTruckId(
                str_pad($formTruck->getTruckId(), 6, "0", STR_PAD_LEFT));

            $fTID= $formTruck->getTruckId();
            //check if truckId has already been used
            $truckIdUsed = (0 < count($em->getRepository(Truck::class)
                ->findBy(array('truckId' => $formTruck->getTruckId()))));

            // Add custom error to form
            if($truckIdUsed)
            {
                $addform->addError(new FormError("A Truck with the ID \"$fTID\" has already been added."));
            }
            else
            {
                //save truck
                $em->persist($formTruck);
                $em->flush();
                //$truckId = $formTruck->getId();

                /* refresh the trucks to display the new one.
                  Because the trucks are set to cascade refresh it will reload them too */
                //THIS IS ACTUALLY DONE BY IN FILTER.JS

                // Wipe the form by creating a new one
                $formTruck = new Truck();
                $addform = $this->createForm(TruckType::class, $formTruck);

                // Add a success message above the form
                $showSuccess = true;
            }
        }

        $filteredtrucks = $this->jsonFilterAction();//$filterQuery);

        return $this->render('truck/util.html.twig',
            array('add_truck_form'=>$addform->createView(),
             //'filterform'=>$filterForm->createView(),
             'formtruck'=>$formTruck,
             'inittrucks'=>'['.explode('[',$filteredtrucks)[1],
             'showSuccess'=>$showSuccess));
    }

    /**
     * STORY40A
     * Displays a form to edit an existing truck entity.
     * Called when the save button is pressed
     *
     * @Route("/edit/{id}", name="truck_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, $id=null)
    {
        if(is_null($id))
            $id = intval($request->get('id'));

        // = $id;

        $em = $this->getDoctrine()->getManager();
        $repo = $em->getRepository(Truck::class);
        $truck = $repo->findOneById($id);

        $initialId = -1;
        $editForm  = null;

        if(!is_null($truck))
        {
            $initialId = $truck->getTruckId();
            $editForm = $this->createForm('AppBundle\Form\TruckEditType', $truck);
            $editForm->handleRequest($request);
        }


        if(is_null($editForm))
        {
            //show an error (truck not found, form not generated)
        }
        else
        {
            // If the user has entered valid information and clicked the "Save" button
            if ($editForm->isSubmitted() && $editForm->isValid())
            {
                // Get the ID that the user has set and pad it if it isn't 6 characters already
                $truck->setTruckId(
                    str_pad($truck->getTruckId(), 6, "0", STR_PAD_LEFT));

                $fTID = $truck->getTruckId();

                $truckIdUsed = false;

                if($initialId != $fTID)
                {
                    //check if truckId has already been used
                    $truckIdUsed = (0 < count($em->getRepository(Truck::class)
                        ->findBy(array('truckId' => $truck->getTruckId()))));
                }

                // Add custom error to form
                if($truckIdUsed)
                {
                    $truck->setTruckId($initialId);
                    $editForm = $this->createForm('AppBundle\Form\TruckEditType', $truck);
                    $editForm->addError(new FormError("The Truck ID \"$fTID\" is already in use, reverted to \"$initialId\"."));
                }
                else
                {
                    //save truck
                    $em->persist($truck);
                    $em->flush();

                    return $this->redirectToRoute('truck_util', array('id' => $truck->getId()));
                }
            }
        }

        return $this->render('truck/edit.html.twig', array(
            'truckid'=> $truck->getTruckId(),
            'truck' => $truck,
            'edit_form' => $editForm->createView(),
            //'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * STORY40A
     * Handles the removal of a truck
     * @param Request $request
     * @Route("/remove/{id}", name="truck_remove")
     * @Method("POST")
     * maybe use the DELETE method??
     */
    public function removeTruckAction(Request $request, $id=null)
    {
        //if posted
        if($request->getMethod() == 'POST')
        {
            if(is_null($id))
                $id = intval($request->get('id'));

            $em = $this->getDoctrine()->getManager();
            $truckRepo = $em->getRepository(Truck::class);
            $truck = $truckRepo->findOneById($id);

            if($truck != null)
            {
                $em->remove($truck);
                $em->flush();
            }
        }

        //If there wasn't a success anywhere, redirect to the contact search page
        return $this->redirectToRoute("truck_util");
    }

    /**
     * STORY40A
     * Much like jsonSearchAction, returns all if no query spec'd, returns a search if spec'd
     * @Route("/jsonfilter/", name="truck_jsonfilter_empty")
     * @Route("/jsonfilter/{searchQuery}", name="truck_jsonfilter")
     * @Method("GET")
     */
    public function jsonFilterAction($searchQuery = "")
    {
        // Clean the input
        $searchQuery = htmlentities($searchQuery);

        $truckSearches='';
        $results = $this->json(array());

        // if the string to query onn is less than or equal to 100 characters
        if(strlen($searchQuery) <= 100)// && strlen($searchQuery) > 0)
        {
            // create a cleaner to cleanse the search query
            $cleaner = new Cleaner();

            // cleanse the query
            $cleanQuery = $cleaner->cleanSearchQuery($searchQuery);

            // get an entity manager
            $em = $this->getDoctrine()->getManager();

            // Use the repository to query for the records we want.
            // Store those records into an array.
            $truckSearches = $em->getRepository(Truck::class)->truckFilter($cleanQuery);

            // create a SearchNarrower to narrow down our searches
            $searchNarrower = new SearchNarrower();

            // narrow down our searches, and store their values along side their field values
            $searchedData = $searchNarrower->narrower($truckSearches, $cleanQuery, new Truck());

            $encoder = new JsonEncoder();
            $normalizer = new ObjectNormalizer();
            $normalizer->setIgnoredAttributes(array("dateModified", "__initializer__", "__cloner__", "__isInitialized__")); //idk why i need these ones, but I do..
            $serializer = new Serializer(array($normalizer), array($encoder));

            // Return the results as a json object
            // NOTE: Serializer service needs to be enabled for this to work properly
            $results = JsonResponse::fromJsonString($serializer->serialize($searchedData, 'json'));
        }
        else
        {
            $results = $this->json(array());
        }

        // string over 100, return empty array.
        return $results;
    }
}