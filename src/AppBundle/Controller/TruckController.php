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
     * @Route("/", name="truck_manage")
     * @param Request $request
     */
    public function indexAction(Request $request)
    {
        // Get the entity manager so we can interact with trucks in the DB
        $em = $this->getDoctrine()->getManager();

        $formTruck = new Truck();

        $filterForm = $this->createFormBuilder()
            ->setAction($this->generateUrl('truck_manage'))
            ->getForm()->add('filter_list',null,
                        array('required' => false,));

        $filterForm->handleRequest($request);
        // Create a default filterQuery with nothing in it
        $filterQuery=null;
        // If the user has typed in the filter box
        if($filterForm->isSubmitted())
        {
            // Set the filterQuery to be the information in the filter box
            //VALIDATE QUERY
            $filterQuery = $filterForm->getData();
        }

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

            //check if truckId has already been used
            $truckIdUsed = (0 < count($em->getRepository(Truck::class)
                ->findBy(array('truckId' => $formTruck->getTruckId()))));

            // Add custom error to form
            if($truckIdUsed)
            {
                $addform->addError(new FormError('A Truck with the ID; [truckId] has already been added.'));
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

        $filteredtrucks = $this->jsonFilterAction($filterQuery);

        return $this->render('truck/util.html.twig',
            array('form'=>$addform->createView(),
             'filterform'=>$filterForm->createView(),
             'formtruck'=>$formTruck,
             'inittrucks'=>'['.explode('[',$filteredtrucks)[1],
             'showSuccess'=>$showSuccess));
    }

    /**
     * Creates a new truck entity.
     * Called when the "Add" button is pressed
     *
     * @Route("/new", name="truck_new")
     * @Method({"GET", "POST"})
     */
    //public function newAction(Request $request)
    //{
    //    $truck = new Truck();
    //    $form = $this->createForm('AppBundle\Form\TruckType', $truck);
    //    $form->handleRequest($request);

    //    if ($form->isSubmitted() && $form->isValid()) {
    //        $em = $this->getDoctrine()->getManager();
    //        $em->persist($truck);
    //        $em->flush();

    //        return $this->redirectToRoute('truck_show', array('id' => $truck->getId()));
    //    }

    //    return $this->render('truck/new.html.twig', array(
    //        'truck' => $truck,
    //        'form' => $form->createView(),
    //    ));
    //}


    /**
     * Filter all trucks in the list by
     *
     * @Route("/", name="truck_filter")
     * @param Request $request
     * @param String $filter
     * @Method({"GET", "POST"})
     */
    /*
    public function filterAction(Request $request, $filter)
    {
        $truck = (new Truck())
                    ->setTruckId($filter)
                    ->setType($filter);

        $deleteForm = $this->createDeleteForm($truck);
        $editForm = $this->createForm('AppBundle\Form\TruckType', $truck);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('truck_edit', array('id' => $truck->getId()));
        }

        return $this->render('truck/edit.html.twig', array(
            'truck' => $truck,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }
    */



    /**
     * Displays a form to edit an existing truck entity.
     * Called when the save button is pressed
     *
     * @Route("/{id}/edit", name="truck_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, Truck $truck)
    {
        $deleteForm = $this->createDeleteForm($truck);
        $editForm = $this->createForm('AppBundle\Form\TruckType', $truck);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('truck_edit', array('id' => $truck->getId()));
        }

        return $this->render('truck/edit.html.twig', array(
            'truck' => $truck,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }



    /**
     * Deletes a truck entity.
     * Called when the user presses a delete button
     *
     * @Route("/{id}", name="truck_removal")
     * @Method("DELETE")
     */
    //public function deleteAction(Request $request, Truck $truck)
    //{
    //    $form = $this->createDeleteForm($truck);
    //    $form->handleRequest($request);

    //    if ($form->isSubmitted() && $form->isValid()) {
    //        $em = $this->getDoctrine()->getManager();
    //        $em->remove($truck);
    //        $em->flush();
    //    }

    //    return $this->redirectToRoute('truck_index');
    //}


    /**
     * USELESS GARBAGE
     * Story 40a
     *
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
            $normalizer->setIgnoredAttributes(array("__initializer__", "__cloner__", "__isInitialized__")); //idk why i need these ones, but I do..
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