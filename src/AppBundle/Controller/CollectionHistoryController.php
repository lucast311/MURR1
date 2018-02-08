<?php

namespace AppBundle\Controller;

use AppBundle\Entity\CollectionHistory;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;use Symfony\Component\HttpFoundation\Request;

/**
 * Collectionhistory controller.
 *
 * @Route("collectionhistory")
 */
class CollectionHistoryController extends Controller
{
    /**
     * Lists all collectionHistory entities.
     *
     * @Route("/", name="collectionhistory_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $collectionHistories = $em->getRepository('AppBundle:CollectionHistory')->findAll();

        return $this->render('collectionhistory/index.html.twig', array(
            'collectionHistories' => $collectionHistories,
        ));
    }

    /**
     * Creates a new collectionHistory entity.
     *
     * @Route("/new", name="collectionhistory_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $collectionHistory = new Collectionhistory();
        $form = $this->createForm('AppBundle\Form\CollectionHistoryType', $collectionHistory);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($collectionHistory);
            $em->flush();

            return $this->redirectToRoute('collectionhistory_show', array('id' => $collectionHistory->getId()));
        }

        return $this->render('collectionhistory/new.html.twig', array(
            'collectionHistory' => $collectionHistory,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a collectionHistory entity.
     *
     * @Route("/{id}", name="collectionhistory_show")
     * @Method("GET")
     */
    public function showAction(CollectionHistory $collectionHistory)
    {
        $deleteForm = $this->createDeleteForm($collectionHistory);

        return $this->render('collectionhistory/show.html.twig', array(
            'collectionHistory' => $collectionHistory,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing collectionHistory entity.
     *
     * @Route("/{id}/edit", name="collectionhistory_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, CollectionHistory $collectionHistory)
    {
        $deleteForm = $this->createDeleteForm($collectionHistory);
        $editForm = $this->createForm('AppBundle\Form\CollectionHistoryType', $collectionHistory);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('collectionhistory_edit', array('id' => $collectionHistory->getId()));
        }

        return $this->render('collectionhistory/edit.html.twig', array(
            'collectionHistory' => $collectionHistory,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a collectionHistory entity.
     *
     * @Route("/{id}", name="collectionhistory_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, CollectionHistory $collectionHistory)
    {
        $form = $this->createDeleteForm($collectionHistory);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($collectionHistory);
            $em->flush();
        }

        return $this->redirectToRoute('collectionhistory_index');
    }

    /**
     * Creates a form to delete a collectionHistory entity.
     *
     * @param CollectionHistory $collectionHistory The collectionHistory entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(CollectionHistory $collectionHistory)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('collectionhistory_delete', array('id' => $collectionHistory->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}
