<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Container;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;use Symfony\Component\HttpFoundation\Request;

/**
 * Container controller.
 *
 * @Route("container")
 */
class ContainerController extends Controller
{
    /**
     * Lists all container entities.
     *
     * @Route("/", name="container_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $containers = $em->getRepository('AppBundle:Container')->findAll();

        return $this->render('container/index.html.twig', array(
            'containers' => $containers,
        ));
    }

    /**
     * Creates a new container entity.
     *
     * @Route("/new", name="container_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $container = new Container();
        $form = $this->createForm('AppBundle\Form\ContainerType', $container);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($container);
            $em->flush();

            return $this->redirectToRoute('container_show', array('id' => $container->getId()));
        }

        return $this->render('container/new.html.twig', array(
            'container' => $container,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a container entity.
     *
     * @Route("/{id}", name="container_show")
     * @Method("GET")
     */
    public function showAction(Container $container)
    {
        $deleteForm = $this->createDeleteForm($container);

        return $this->render('container/show.html.twig', array(
            'container' => $container,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing container entity.
     *
     * @Route("/{id}/edit", name="container_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, Container $container)
    {
        //generate the necessary forms
        $deleteForm = $this->createDeleteForm($container);
        $editForm = $this->createForm('AppBundle\Form\ContainerEditType', $container);
        $editForm->handleRequest($request);

        //if the form is valid and submitted, edit the container
        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            //redirect to the display page for this edited container
            return $this->redirectToRoute('container_show', array('id' => $container->getId()));
        }

        //render the page
        return $this->render('container/edit.html.twig', array(
            'container' => $container,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a container entity.
     *
     * @Route("/{id}", name="container_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, Container $container)
    {
        $form = $this->createDeleteForm($container);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($container);
            $em->flush();
        }

        return $this->redirectToRoute('container_index');
    }

    /**
     * Creates a form to delete a container entity.
     *
     * @param Container $container The container entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Container $container)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('container_delete', array('id' => $container->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}
