<?php
namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Form\Type\CommunicationType;
use AppBundle\Entity\Communication;


class CommunicationController extends Controller
{
    /**
     * @Route("/communication", name = "new communication")
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

            //get the doctrine repository
            //$repo = $this->getDoctrine()->getRepository(Communication::class);
            ////insert into the database
            //$repo->insert($communication);

            //create a new blank form to erase the old data
            $form = $this->createForm(CommunicationType::class, new Communication());

            //let the user know that the communication was added
            $added = true;
        }

        return $this->render('communication/newComm.html.twig', [
    'base_dir' => realpath($this->getParameter('kernel.project_dir')).DIRECTORY_SEPARATOR,
    'form' => $form->createView(),
    'added'=>$added]);
    }

}