<?php

namespace AppBundle\Controller;

use AppBundle\Entity\PickupInfo;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;use Symfony\Component\HttpFoundation\Request;

/**
 * Pickupinfo controller.
 *
 * @Route("pickupinfo")
 */
class PickupInfoController extends Controller
{
    /**
     * Lists all pickupInfo entities.
     *
     * @Route("/", name="pickupinfo_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $pickupInfos = $em->getRepository('AppBundle:PickupInfo')->findAll();

        return $this->render('pickupinfo/index.html.twig', array(
            'pickupInfos' => $pickupInfos,
        ));
    }

    /**
     * Creates a new pickupInfo entity.
     *
     * @Route("/new", name="pickupinfo_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $pickupInfo = new Pickupinfo();
        $form = $this->createForm('AppBundle\Form\PickupInfoType', $pickupInfo);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($pickupInfo);
            $em->flush();

            return $this->redirectToRoute('pickupinfo_show', array('id' => $pickupInfo->getId()));
        }

        return $this->render('pickupinfo/new.html.twig', array(
            'pickupInfo' => $pickupInfo,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a pickupInfo entity.
     *
     * @Route("/{id}", name="pickupinfo_show")
     * @Method("GET")
     */
    public function showAction(PickupInfo $pickupInfo)
    {
        $deleteForm = $this->createDeleteForm($pickupInfo);

        return $this->render('pickupinfo/show.html.twig', array(
            'pickupInfo' => $pickupInfo,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing pickupInfo entity.
     *
     * @Route("/{id}/edit", name="pickupinfo_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, PickupInfo $pickupInfo)
    {
        $deleteForm = $this->createDeleteForm($pickupInfo);
        $editForm = $this->createForm('AppBundle\Form\PickupInfoType', $pickupInfo);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('pickupinfo_edit', array('id' => $pickupInfo->getId()));
        }

        return $this->render('pickupinfo/edit.html.twig', array(
            'pickupInfo' => $pickupInfo,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a pickupInfo entity.
     *
     * @Route("/{id}", name="pickupinfo_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, PickupInfo $pickupInfo)
    {
        $form = $this->createDeleteForm($pickupInfo);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($pickupInfo);
            $em->flush();
        }

        return $this->redirectToRoute('pickupinfo_index');
    }

    /**
     * Creates a form to delete a pickupInfo entity.
     *
     * @param PickupInfo $pickupInfo The pickupInfo entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(PickupInfo $pickupInfo)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('pickupinfo_delete', array('id' => $pickupInfo->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}
