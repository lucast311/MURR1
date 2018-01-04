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

            if($communication->getContact() <= 0) //if the communication ID is not a real ID
            {
                $communication->setContact(null);
            }
            if($communication->getProperty() <= 0)
            {
                $communication->setProperty(null);
            }

            //PLEASE RETURN TO ME WHEN USERS ARE IMPLEMENTED
            $communication->setUser(1); //set the user ID


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
     *
     * Summary of viewAction
     * @param mixed $comId
     */
    public function viewAction($comId = null){

    }
}