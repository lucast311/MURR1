<?php
namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Form\Type\CommunicationType;
use AppBundle\Entity\Communication;

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

    }
}