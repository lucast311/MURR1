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
    public function manageAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $trucks = $em->getRepository(Truck::class)->findAll();
        $formTruck = new Truck();

        $form = $this->createForm(TruckType::class, $formTruck);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            while(strlen($formTruck->getTruckId())<6)
                $formTruck->setTruckId('0'.$formTruck->getTruckId());

            $truckIdExists = false;
            //loop through the existing trucks
            foreach ($trucks as $truck)
            {
                //if the truckId already exists, break and indiciate error
                if($formTruck->getTruckId() === $truck->getTruckId()){
                    $truckIdExists = true;
                    break;
                }
            }

            //Add custom error to form
            if($truckIdExists){
                $form->addError(new FormError('A Truck with the ID; [truckId] has already been added.'));
            }
            else{
                //refresh the trucks
                $repo = $em->getRepository(Truck::class);
                $repo->save($formTruck);
                //refresh the trucks to display the new one
                //And since the trucks are set to cascade refresh it will reload them too
                $trucks = $repo->findAll();
                foreach ($trucks as $truck)
                {
                    $em->refresh($truck);
                }

                //Wipe the form by creating a new one
                $formTruck = new Truck();
                $form = $this->createForm(TruckType::class, $formTruck);
            }
        }

        return $this->render('truck/util.html.twig',
            array('form'=>$form->createView(),
             'formTruck'=>$formTruck,
             'trucks'=>$trucks));
    }

    /**
     * Creates a new truck entity.
     * Called when the "Add" button is pressed
     *
     * @Route("/new", name="truck_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $truck = new Truck();
        $form = $this->createForm('AppBundle\Form\TruckType', $truck);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($truck);
            $em->flush();

            return $this->redirectToRoute('truck_show', array('id' => $truck->getId()));
        }

        return $this->render('truck/new.html.twig', array(
            'truck' => $truck,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a truck entity.
     * Called when the filter box is used
     *
     * @Route("/{id}", name="truck_show")
     * @Method("GET")
     */
    public function showAction(Truck $truck)
    {
        $deleteForm = $this->createDeleteForm($truck);

        return $this->render('truck/show.html.twig', array(
            'truck' => $truck,
            'delete_form' => $deleteForm->createView(),
        ));
    }

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
    public function deleteAction(Request $request, Truck $truck)
    {
        $form = $this->createDeleteForm($truck);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($truck);
            $em->flush();
        }

        return $this->redirectToRoute('truck_index');
    }

    /**
     * Creates a form to delete a truck entity.
     *
     * @param Truck $truck The truck entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Truck $truck)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('truck_delete', array('id' => $truck->getId())))
            ->setMethod('DELETE')
            ->getForm();
    }
}