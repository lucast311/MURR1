<?php
namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Form\CommunicationType;
use AppBundle\Entity\Communication;
use AppBundle\Entity\Contact;
use AppBundle\Entity\Property;
use AppBundle\Entity\Address;
use AppBundle\Entity\Container;
use AppBundle\Entity\ContactProperty;
use AppBundle\Services\Cleaner;
use AppBundle\Services\SearchNarrower;
use AppBundle\Services\RecentUpdatesHelper;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Serializer;
use Doctrine\Common\Annotations\AnnotationReader;
use Symfony\Component\Serializer\Mapping\Loader\AnnotationLoader;


/**
 * This controller will be repsonsible for the various requirements of interacting with
 * Communication data (add, edit, view, etc.)
 */
class CommunicationController extends Controller
{
    /**
     * story10a
     * Front end for searching for a communication.
     *
     * @Route("/communication/search", name="communication_search")
     * @Method("GET")
     */
    public function searchAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        // get the RecentUpdates service to query for the 10 most recently updated communications
        $recentUpdates = new RecentUpdatesHelper();

        // the service takes in an EntityManager, and the name of the Entity
        $tenRecent = $recentUpdates->tenMostRecent($em, 'AppBundle:Communication');

        // Get if it is in a search to view or if it is a search to insert
        $isPopup = ($request->query->get("isPopup")) == "true" ? true : false;
        // Render the twig with required data
        return $this->render('communication/searchCommunication.html.twig', array(
            'viewURL' => '/communication/',
            'isPopup' => $isPopup,
            'defaultTen' => $tenRecent
        ));
    }

    /**
     * This route will be responsible for loading and submitting the form responsible
     * for entering Communication Data
     * @Route("/communication/new", name = "new_communication")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction(Request $request)
    {
        $communication = new Communication();

        $form = $this->createForm(CommunicationType::class, $communication);

        $form->handleRequest($request);

        $added = false;

        if($form->isSubmitted() && $form->isValid())
        {
            //get the data from the form
            $communication = $form->getData();



            //create a new blank form to erase the old data
            $form = $this->createForm(CommunicationType::class, new Communication());


            $repo = $this->getDoctrine()->getRepository(Communication::class);
            //insert into the database
            $repo->insert($communication);


            //let the user know that the communication was added
            $added = true;

            //if it was sent from a modal, redirect to the property it came from
            //This happens on a success, so we can safely redirect and not worry about errors
            if($request->get("isModal") == 1)
            {
                return $this->redirectToRoute("property_view",
                    array("propertyId"=>$communication->getProperty()->getId()));
            }
        }

        //if it was sent from a modal, forward the request to the view action (to show errors)
        if($request->get("isModal") == 1)
        {
            //return $this->redirectToRoute("property_view",
            //    array("propertyId"=>$communication->getProperty()->getId(),
            //    "addCommunicationForm" => $form));
            return $this->forward('AppBundle:Property:view',
                array("propertyId"=>$communication->getProperty()->getId(),
                "addCommunicationForm" => $form));
        }

        return $this->render('communication/newComm.html.twig', [
    'base_dir' => realpath($this->getParameter('kernel.project_dir')).DIRECTORY_SEPARATOR,
    'form' => $form->createView(),
    'added'=>$added]);
    }

    /**
     * Summary of editAction
     * @Route ("/communication/{commId}/edit", name = "communication_edit")
     * @Method({"GET","POST"})
     */
    public function editAction(Request $request, $commId = null)
    {
        // Get the entity manager
        $em = $this->getDoctrine()->getManager();

        //get the repository for communications
        $repo = $em->getRepository(Communication::class);

        $communication = $repo->findOneById($commId);

        // Get the specific Communication
        $comm = $repo->findOneById($commId);

        //variable that willl handle what type of error will be shown
        $errorType = null;

        $communicationId = -1;
        if ($comm != null)
        {
        	$communicationId = $comm->getId();
        }


        if($comm == null) $errorType="notfound";

        $form = $this->createForm(CommunicationType::class, $comm);

        $form->handleRequest($request);


        if($form->isSubmitted() && $form->isValid())
        {
            //get the data from the form
            $communicationData = $form->getData();

            //insert into the database
            $repo->insert($communicationData);

            //redirect to the view page
            return $this->redirectToRoute("communication_view",array("comId" => $communicationData->getId()));
        }

        return $this->render('communication/editComm.html.twig', [
            'base_dir' => realpath($this->getParameter('kernel.project_dir')).DIRECTORY_SEPARATOR,
            'form' => $form->createView(),
            'errorType'=>$errorType,
            'communicationId'=>$communicationId,
            'communication'=>$communication]);
    }

    /**
     * Deletes a Communication entity.
     *
     * @Route("/delete/{id}", name="communication_delete")
     * @Method("POST")
     */
    public function deleteAction(Communication $communication)
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($communication);
        $em->flush();

        return $this->redirectToRoute('communication_search');
    }


    /**
     * Story 11b
     * Controller responsible for viewing a communication
     * Summary of viewAction
     * @param mixed $comId
     * @Route ("/communication/{comId}", name = "communication_view")
     * @Route ("/communication/")
     */
    public function viewAction($comId = null){
        // Get the entity manager
        $em = $this->getDoctrine()->getManager();
        // Get the specific Communication
        $comm = $em->getRepository(Communication::class)->findOneById($comId);

        //variable that willl handle what type of error will be shown
        $errorType = null;

        if($comm == null) $errorType="notfound";
        if($comId == null) $errorType="noid";

        //render the page
        return $this->render('communication/viewComm.html.twig',
            array('comm'=>$comm, 'errorType'=>$errorType));

    }

    /**
     * Story 11c
     * A function that will take in a string to separate, and then pass
     *  into the repository as an array. It will then narrow the results further,
     *  and display those results to a page containing a json header.
     * @param string $searchQuery - the string to split apart into the individual search queries.
     *
     * @Route("/communication/jsonsearch/", name="communication_jsonsearch_empty")
     * @Route("/communication/jsonsearch/{searchQuery}", name="communication_jsonsearch")
     * @Method("GET")
     */
    public function jsonSearchAction($searchQuery = "")
    {
        if($searchQuery != "")
        {
            // Clean the input
            $searchQuery = htmlentities($searchQuery);

            // if the string to query onn is less than or equal to 100 characters
            if(strlen($searchQuery) <= 500 && !empty($searchQuery))
            {
                // create a cleaner to cleanse the search query
                $cleaner = new Cleaner();

                // cleanse the query
                $cleanQuery = $cleaner->cleanSearchQuery($searchQuery);

                // get an entity manager
                $em = $this->getDoctrine()->getManager();


                $communicationJoins = array(new Communication(), new Property());


                // Use the repository to query for the records we want.
                // Store those records into an array.
                $communicationSearches = $em->getRepository(Communication::class)->communicationSearch($cleanQuery);

                // create a SearchNarrower to narrow down our searches
                $searchNarrower = new SearchNarrower();

                // narrow down our searches, and store their values along side their field values
                $searchedData = $searchNarrower->narrower($communicationSearches, $cleanQuery, new Communication());

                // Return the results as a json object
                // NOTE: Serializer service needs to be enabled for this to work properly
                $encoder = new JsonEncoder();
                $normalizer = new ObjectNormalizer();

                // We used to get a circular reference error. This line prevents it.
                $normalizer->setCircularReferenceHandler(function($object){return $object->getDate();});

                // Don't display the 'property' data as JSON. Makes it more human readable.
                $normalizer->setIgnoredAttributes(array("property", "dateModified"));
                $serializer = new Serializer(array($normalizer), array($encoder));

                return JsonResponse::fromJsonString($serializer->serialize($searchedData, 'json'));
            }
        }
        else
        {
            // get an entity manager
            $em = $this->getDoctrine()->getManager();

            // get the RecentUpdates service to query for the 10 most recently updated communications
            $recentUpdates = new RecentUpdatesHelper();

            // the service takes in an EntityManager, and the name of the Entity
            $tenRecent = $recentUpdates->tenMostRecent($em, 'AppBundle:Communication');

            // Return the results as a json object
            // NOTE: Serializer service needs to be enabled for this to work properly
            $encoder = new JsonEncoder();
            $normalizer = new ObjectNormalizer();

            // We used to get a circular reference error. This line prevents it.
            $normalizer->setCircularReferenceHandler(function($object){return $object->getDate();});

            // Don't display the 'property' data as JSON. Makes it more human readable.
            $normalizer->setIgnoredAttributes(array("property", "dateModified"));
            $serializer = new Serializer(array($normalizer), array($encoder));

            return JsonResponse::fromJsonString($serializer->serialize($tenRecent, 'json'));
        }

        // string over 100, return empty array.
        return $this->json(array());
    }
}