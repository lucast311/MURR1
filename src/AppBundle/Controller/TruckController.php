<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Truck;
use AppBundle\Form\TruckType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
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
     * Lists all truck entities.
     *
     * @Route("/", name="truck_manage")
     * @param Request $request
     */
    public function indexAction(Request $request)
    {
        // Get the entity manager so we can interact with trucks in the DB
        $em = $this->getDoctrine()->getManager();

        // Grab all trucks in the Truck table
        $trucks = $em->getRepository(Truck::class)->findAll();
        $formTruck = new Truck();

        $filterForm = $this->createFormBuilder()
            ->setAction($this->generateUrl('truck_filter'))
            ->getForm();
        $filterForm->handleRequest($request);
        // Create a default filterQuery with nothing in it
        $filterQuery="";
        // If the user has typed in the filter box
        if($filterForm->isSubmitted())
        {
            // Set the filterQuery to be the information in the filter box
            $filterQuery = $filterForm->getData();

            // REMOVE
            var_dump($filterQuery);
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

            $truckIdExists = false;
            // loop through the existing trucks
            foreach ($trucks as $truck)
            {
                //if the truckId already exists, break and indicate error
                if($formTruck->getTruckId() === $truck->getTruckId()){
                    $truckIdExists = true;
                    break;
                }
            }

            // Add custom error to form
            if($truckIdExists)
            {
                $addform->addError(new FormError('A Truck with the ID; [truckId] has already been added.'));
            }
            else
            {
                // refresh the trucks
                $repo = $em->getRepository(Truck::class);
                $truckId = $repo->save($formTruck);
                /* refresh the trucks to display the new one.
                  Because the trucks are set to cascade refresh it will reload them too */
                $trucks = $repo->findAll();
                foreach ($trucks as $truck)
                {
                    $em->refresh($truck);
                }

                // Wipe the form by creating a new one
                $formTruck = new Truck();
                $addform = $this->createForm(TruckType::class, $formTruck);

                // Add a success message above the form
                $showSuccess = true;
            }
        }

        return $this->render('truck/util.html.twig',
            array('form'=>$addform->createView(),
             'filterform'=>$filterForm->createView(),
             'formTruck'=>$formTruck,
             'trucks'=>$trucks,
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
}