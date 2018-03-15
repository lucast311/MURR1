<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Container;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;use Symfony\Component\HttpFoundation\Request;
use AppBundle\Services\Cleaner;
use AppBundle\Services\SearchNarrower;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Serializer;

/**
 * Container controller.
 *
 * @Route("container")
 */
class ContainerController extends Controller
{
    /**
     * story12e
     * Front end for searching for a container.
     *
     * @Route("/search", name="container_search")
     * @Method("GET")
     */
    public function searchAction(Request $request)
    {
        // Get if it is in a search to view or if it is a search to insert
        $isPopup = ($request->query->get("isPopup")) == "true" ? true : false;
        // Render the twig with required data
        return $this->render('container/searchContainer.html.twig', array(
            'viewURL' => '/container/',
            'isPopup' => $isPopup
        ));
    }

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
    public function showAction($id=null)
    {
        $repo = $this->getDoctrine()->getManager()->getRepository(Container::class);
        $container = $repo->findOneById($id);

        if ($container != null)
        {
            $deleteForm = $this->createDeleteForm($container);

            return $this->render('container/show.html.twig', array(
            'container' => $container,
            'delete_form' => $deleteForm->createView(),
            'invalid_id_error'=>false
            ));
        }

            return $this->render('container/show.html.twig', array(
                'invalid_id_error'=>true
            ));

    }

    /**
     * Displays a form to edit an existing container entity.
     *
     * @Route("/{id}/edit", name="container_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, $id=null)
    {

        $repo = $this->getDoctrine()->getManager()->getRepository(Container::class);
        $container = $repo->findOneById($id);

        if($container != null)
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
                'invalid_id_error' => false
            ));
        }

        //This point will only be reached if the container is null, meaning we will be displaying an error
        return $this->render('container/edit.html.twig', array(
            'container' => $container,
            'invalid_id_error' => true
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

    /**
     * Story 12d
     * A function that will take in a string to separate, and then pass
     *  into the repository as an array. It will then narrow the results further,
     *  and display those results to a page containing a json header.
     * @param string $searchQuery - the string to split apart into the individual search queries.
     *
     * @Route("/jsonsearch/", name="container_jsonsearch_empty")
     * @Route("/jsonsearch/{searchQuery}", name="container_jsonsearch")
     * @Method("GET")
     */
    public function jsonSearchAction($searchQuery = "")
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



            // Use the repository to query for the records we want.
            // Store those records into an array.
            $containerSearches = $em->getRepository(Container::class)->containerSearch($cleanQuery);

            // create a SearchNarrower to narrow down our searches
            $searchNarrower = new SearchNarrower();

            // narrow down our searches, and store their values along side their field values
            $searchedData = $searchNarrower->narrower($containerSearches, $cleanQuery, new Container());

            // Return the results as a json object
            // NOTE: Serializer service needs to be enabled for this to work properly
            $encoder = new JsonEncoder();
            $normalizer = new ObjectNormalizer();

            // We used to get a circular reference error. This line prevents it.
            $normalizer->setCircularReferenceHandler(function($object){return $object->getDate();});

            // Don't display the 'property' data as JSON. Makes it more human readable.
            $normalizer->setIgnoredAttributes(array("property", "structure", "address"));
            $serializer = new Serializer(array($normalizer), array($encoder));

            return JsonResponse::fromJsonString($serializer->serialize($searchedData, 'json'));
        }

        // string over 100, return empty array.
        return $this->json(array());
    }


}
