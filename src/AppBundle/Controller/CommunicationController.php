<?php
namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Form\Type\CommunicationType;
use AppBundle\Entity\Communication;
use AppBundle\Services\Cleaner;
use AppBundle\Services\SearchNarrower;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Serializer;


/**
 * This controller will be repsonsible for the various requirements of interacting with
 * Communication data (add, edit, view, etc.)
 */
class CommunicationController extends Controller
{
    /**
     * This route will be responsible for loading and submitting the form responsible
     * for entering Communication Data
     * @Route("/communication/new", name = "new communication")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction(Request $request)
    {
        $form = $this->createForm(CommunicationType::class, new Communication());

        $form->handleRequest($request);

        $added = false;

        if($form->isSubmitted() && $form->isValid())
        {
            //get the data from the form
            $communication = $form->getData();



            //create a new blank form to erase the old data
            $form = $this->createForm(CommunicationType::class, new Communication());

            //PLEASE RETURN TO ME WHEN USERS ARE IMPLEMENTED
            //$communication->setUser(1); //set the user ID


            //get the doctrine repository
            $repo = $this->getDoctrine()->getRepository(Communication::class);
            //insert into the database
            $repo->insert($communication);


            //let the user know that the communication was added
            $added = true;
        }

        return $this->render('communication/newComm.html.twig', [
    'base_dir' => realpath($this->getParameter('kernel.project_dir')).DIRECTORY_SEPARATOR,
    'form' => $form->createView(),
    'added'=>$added]);
    }

    /**
     * Story 11b
     * Controller responsible for viewing a communication
     * Summary of viewAction
     * @param mixed $comId
     * @Route ("/communication/view/{comId}")
     * @Route ("/communication/view/")
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
            $communicationSearches = $em->getRepository(Communication::class)->communicationSearch($cleanQuery);

            // create a SearchNarrower to narrow down our searches
            $searchNarrower = new SearchNarrower();

            // narrow down our searches, and store their values along side their field values
            $searchedData = $searchNarrower->narrower(new Communication(), $communicationSearches, $cleanQuery);

            // Return the results as a json object
            // NOTE: Serializer service needs to be enabled for this to work properly
            $encoder = new JsonEncoder();
            $normalizer = new ObjectNormalizer();
            $normalizer->setIgnoredAttributes(array("properties"));
            $serializer = new Serializer(array($normalizer), array($encoder));

            return JsonResponse::fromJsonString($serializer->serialize($searchedData, 'json'));
        }
        // string over 100, return empty array.
        return $this->json(array());
    }
}