<?php
namespace AppBundle\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use AppBundle\Form\RoutePickupType;
use AppBundle\Form\RouteTemplateType;
use AppBundle\Form\RouteType;

use AppBundle\Entity\RoutePickup;
use AppBundle\Entity\Route as RouteEntity;
use AppBundle\Entity\Route as ContainerRoute;
use AppBundle\Entity\Route as ContainerRouteTemplate;

use AppBundle\Services\Cleaner;
use AppBundle\Services\SearchNarrower;
use AppBundle\Services\RecentUpdatesHelper;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\JsonResponse;

use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Serializer;

/**
 * RouteController short summary.
 *
 * RouteController description.
 *
 * @version 1.0
 * @author cst244
 */


/**
 * Controller that contains the actions for managing a route
 *
 * @Route("route")
 */
class RouteController extends Controller
{
    /**
     * S40C
     * Used to search Routes + Templates
     *
     * @Route("/search", name="route_search")
     * @param Request $request
     * @Method("GET")
     */
    function searchAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        // get the RecentUpdates service to query for the 10 most recently updated containers
        $recentUpdates = new RecentUpdatesHelper();

        // the service takes in an EntityManager, and the name of the Entity
        $tenRecent = $recentUpdates->tenMostRecent($em, 'AppBundle:Route');

        // Get if it is in a search to view or if it is a search to insert
        $isPopup = ($request->query->get("isPopup")) == "true" ? true : false;
        // Render the twig with required data
        return $this->render('route/search.html.twig', array(
            'viewURL' => '/route/edit/',
            'isPopup' => $isPopup,
            'defaultTen' => $tenRecent
        ));
    }

    /**
     * S40C
     * Used to create new Routes
     * @param Request $request
     */
    function newAction(Request $request){

    }

    /**
     * S40C
     * Used to create+edit Route Templates
     * @Route("/template/new", name="route_template")
     * @Route("/template/{templateId}", name="route_template_edit")
     * @param Request $request
     * @param integer $templateId
     */
    function templateAction(Request $request, $templateId = null){
        $em = $this->getDoctrine()->getManager();

        //NOTE THE USE OF THE USING STATEMENT TO MAKE CONTAINER ROUTE A SHORTCUT TO ROUTE
        //Otherwise there is conflicts between the route annotations and the route class
        $routeRepo = $em->getRepository(ContainerRouteTemplate::class);
        $routeTemplate = null;
        if(!is_null($templateId)){
            $routeTemplate = $routeRepo->find($templateId);
        }else{
            $routeTemplate = (new ContainerRouteTemplate())
            ->setRouteId("FRESH")
            ->setTemplate()->setDateModified(new \DateTime("00/00/0000 00:00:00"));
            $em->persist($routeTemplate);
            $em->flush();
            $id = $routeTemplate->getId();
            $routeTemplate->setRouteId("New Template $id");
            $em->persist($routeTemplate);
            $em->flush();
        }

        $preName = $routeTemplate->getRouteId();

        if($routeTemplate != null){
            //Create the routePickupForm and set its route
            $rp = (new RoutePickup())
                ->setRoute($routeTemplate);

            $nameForm = $this->createForm(RouteTemplateType::class, $routeTemplate);

            $pickupForm = $this->createForm(RoutePickupType::class, $rp);
            $pickupForm->handleRequest($request);

            if ($nameForm->isSubmitted() && $nameForm->isValid())
            {
                //return $this->forward('AppBundle:Route:addRoutePickup', array('rp'=>$rp));
                $serialExists = false;
                //loop through the existing pickups
                if(count($routeRepo->findBy(array("routeId"=>$routeTemplate->getRouteId())) > 0))
                {
                    //if name in use
                }else{
                    $routeTemplate->updateModifiedDatetime();
                    $em->persist($routeTemplate);
                    $em->flush();
                }
            }

            if ($pickupForm->isSubmitted() && $pickupForm->isValid())
            {
                //return $this->forward('AppBundle:Route:addRoutePickup', array('rp'=>$rp));
                $serialExists = false;
                //loop through the existing pickups
                if(!is_null($routeTemplate->getPickups())){
                    foreach ($routeTemplate->getPickups() as $pickup)
                    {
                        //if the container already exists, break and indiciate error
                        if($pickup->getContainer()->getContainerSerial() === $rp->getContainer()->getContainerSerial()){
                            $serialExists = true;
                            break;
                        }
                    }
                }


                //Add custom error to form
                if($serialExists){
                    $pickupForm->addError(new FormError('This container already exists in this route'));
                }
                else{
                    $routeTemplate->updateModifiedDatetime();
                    $em->persist($routeTemplate);
                    $em->flush();

                    //get the last pickup, they are already ordered
                    $pickups = $routeTemplate->getPickups();
                    $lastRp = $pickups[count($pickups)-1];

                    $repo = $this->getDoctrine()->getManager()->getRepository(RoutePickup::class);

                    //If there is no last pickup, this pickup needs to go first
                    if($lastRp == null){

                        $rp->setPickupOrder(1);
                    }//If the added pickup order is greater than the last by more than 1, set it to be the last one
                    else if ($rp->getPickupOrder() >= $lastRp->getPickupOrder() + 1){
                        $rp->setPickupOrder($lastRp->getPickupOrder() + 1);
                    }
                    else { //The rp is being inserted in the middle of the list
                        //Increment every route pickup that will be after the current route pickup
                        $repo->updateOrders($templateId, $rp->getPickupOrder(), true);

                        //Refresh all the pickups because they may have been changed
                        foreach ($pickups as $pickup)
                        {
                        	$em->refresh($pickup);
                        }
                    }
                    //set this pickup on the current route
                    //$rp->setRoute($route);
                    $repo->save($rp);

                    //refresh the route to display the new data
                    //And since the pickups are set to cascade refresh it will reload them too
                    $em->refresh($routeTemplate);

                    //Wipe the form by creating a new one
                    $rp = new RoutePickup();
                    $pickupForm = $this->createForm(RoutePickupType::class, $rp);
                }
            }

            return $this->render('route/manageRouteDeluxe.html.twig',
                array('nameform'=>$nameForm->createView(),
                      'pickupform'=>$pickupForm->createView(),
                      'route'=>$routeTemplate,
                      'templatemode'=>true));
        }

        //We only get to this point if the route is not found
        //return the page with an error
        return $this->render('route/manageRouteDeluxe.html.twig',
                array('templatemode'=>true));
    }

    /**
     * S40C
     * Used to add RoutePickups to Routes[Templates]
     * @param Request $request
     */
    function addRoutePickupAction(Request $request){

    }

    // FUTURE FUNCTIONALITY
    ///**
    // * S40? //UNFINISHED STORY
    // * @param Request $request
    // */
    //function associateTruckAction(Request $request){
    //
    //}
    ///**
    // * S40? //UNFINISHED STORY
    // * @param Request $request
    // */
    //function removeTruckAction(Request $request){
    //
    //}
    ///**
    // * S40? //UNFINISHED STORY
    // * @param Request $request
    // */
    //function importCSVAction(Request $request){
    //
    //}

    /**
     * Stub function for the route addAction (index)
     * @Route("/route", name="route_util")
     * @param Request $request
     */
    function indexAction()
    {
        return $this->render("route/addRoute.html.twig");
    }

    /**
     * Story 22b
     * +S40C
     * Used to edit Routes
     * @Route("/{routeId}", name="route_manage")
     * @param Request $request
     * @param integer $routeId
     */
    function editAction(Request $request, $routeId=null){
        $em = $this->getDoctrine()->getManager();

        //NOTE THE USE OF THE USING STATEMENT TO MAKE CONTAINER ROUTE A SHORTCUT TO ROUTE
        //Otherwise there is conflicts between the route annotations and the route class
        $routeRepo = $em->getRepository(ContainerRoute::class);
        $route = $routeRepo->findOneById($routeId);

        if($route != null){
            //Create the routePickupForm and set its route
            $rp = (new RoutePickup())
                ->setRoute($route);
            $form = $this->createForm(RoutePickupType::class, $rp);

            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid())
            {
                //return $this->forward('AppBundle:Route:addRoutePickup', array('rp'=>$rp));
                $serialExists = false;
                //loop through the existing pickups
                foreach ($route->getPickups() as $pickup)
                {
                    //if the container already exists, break and indiciate error
                	if($pickup->getContainer()->getContainerSerial() === $rp->getContainer()->getContainerSerial()){
                        $serialExists = true;
                        break;
                    }
                }

                //Add custom error to form
                if($serialExists){
                    $form->addError(new FormError('This container already exists in this route'));
                }
                else{
                    //get the last pickup, they are already ordered
                    $pickups = $route->getPickups();
                    $lastRp = $pickups[count($pickups)-1];

                    $repo = $this->getDoctrine()->getManager()->getRepository(RoutePickup::class);

                    //If there is no last pickup, this pickup needs to go first
                    if($lastRp == null){

                        $rp->setPickupOrder(1);
                    }//If the added pickup order is greater than the last by more than 1, set it to be the last one
                    else if ($rp->getPickupOrder() >= $lastRp->getPickupOrder() + 1){
                        $rp->setPickupOrder($lastRp->getPickupOrder() + 1);
                    }
                    else { //The rp is being inserted in the middle of the list
                        //Increment every route pickup that will be after the current route pickup
                        $repo->updateOrders($routeId, $rp->getPickupOrder(), true);

                        //Refresh all the pickups because they may have been changed
                        foreach ($pickups as $pickup)
                        {
                        	$em->refresh($pickup);
                        }


                    }
                    //set this pickup on the current route
                    //$rp->setRoute($route);
                    $repo->save($rp);

                    //refresh the route to display the new data
                    //And since the pickups are set to cascade refresh it will reload them too
                    $em->refresh($route);

                    //Wipe the form by creating a new one
                    $rp = new RoutePickup();
                    $form = $this->createForm(RoutePickupType::class, $rp);
                }
            }

            return $this->render('route/manageRoute.html.twig',
                array('form'=>$form->createView(),
                'route'=>$route,
                'invalid_id_error'=>false));
        }

        //We only get to this point if the route is not found
        //return the page with an error
        return $this->render('route/manageRoute.html.twig',
                array('invalid_id_error'=>true));

    }

    /**
     * Story 22c
     * Controller action responsible for removing a pickup from a route
     * @param mixed $id The ID of the route pickup to be removed. Posted from a form.
     *
     * @Route("/route/removecontainer/{id}", name="route_pickup_removal")
     */
    public function deleteRoutePickupAction($id=null){

        //Get the EM and the repos
        $em = $this->getDoctrine()->getManager();
        $repo = $em->getRepository(RoutePickup::class);

        //Get the pickup that will be removed
        $pickup = $repo->findOneById($id);

        //If the pickup actually existed
        if($pickup != null)
        {
            //store the routeId for redirection
            $routeId = $pickup->getRoute()->getId();
            //store the pickup order so that we can decrement everything after it
            $pickupOrder = $pickup->getPickupOrder();

            //remove the pickup
            $repo->remove($pickup);

            //Decrement starting at the pickup that was removed
            $repo->updateOrders($routeId, $pickupOrder, false);

            //redirect back to the route that the container was on
            return $this->redirectToRoute("route_manage",array("routeId" =>$routeId));
        }
        else
        {
            //render the error page
            return $this->render('route/deletePickup.html.twig');
        }
    }

    /**
     * Story S40C
     * A function that will take in a string to separate, and then pass
     *  into the repository as an array. It will then narrow the results further,
     *  and display those results to a page containing a json header.
     * @param string $searchQuery - the string to split apart into the individual search queries.
     *
     * @Route("/jsonsearch/", name="route_jsonsearch_empty")
     * @Route("/jsonsearch/{searchQuery}", name="route_jsonsearch")
     * @Method("GET")
     */
    public function jsonSearchAction($searchQuery = "", $template = false)
    {
        // get an entity manager
        $em = $this->getDoctrine()->getManager();

        if($searchQuery != "")
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

                // Use the repository to query for the records we want.
                // Store those records into an array.
                $routeSearches = $em->getRepository(RouteEntity::class)->routeSearch($cleanQuery);

                // create a SearchNarrower to narrow down our searches
                $searchNarrower = new SearchNarrower();

                // narrow down our searches, and store their values along side their field values
                $searchedData = $searchNarrower->narrower($routeSearches, $cleanQuery, new RouteEntity());

                // Return the results as a json object
                // NOTE: Serializer service needs to be enabled for this to work properly
                $encoder = new JsonEncoder();
                $normalizer = new ObjectNormalizer();

                // We used to get a circular reference error. This line prevents it.
                //$normalizer->setCircularReferenceHandler(function($object){return $object->getDate();});

                // Don't display the 'pickups' or 'dateModified' data as JSON. Makes it more human readable.
                $normalizer->setIgnoredAttributes(array("pickups", "dateModified"));
                $serializer = new Serializer(array($normalizer), array($encoder));

                return JsonResponse::fromJsonString($serializer->serialize($searchedData, 'json'));
            }
        }
        else
        {
            //get the recentUpdates service to query for the 10 most recently updated routes
            $recentUpdates = new RecentUpdatesHelper();

            //The service takes in an entitymanager, and the name of the entity
            $tenRecent = $recentUpdates->TenMostRecent($em, 'AppBundle:Route');

            // Return the results as a json object
            // NOTE: Serializer service needs to be enabled for this to work properly
            $encoder = new JsonEncoder();
            $normalizer = new ObjectNormalizer();

            // We used to get a circular reference error. This line prevents it.
            //$normalizer->setCircularReferenceHandler(function($object){return $object->getDate();});

            // Don't display the 'pickups' or 'dateModified' data as JSON. Makes it more human readable.
            $normalizer->setIgnoredAttributes(array("pickups", "dateModified"));
            $serializer = new Serializer(array($normalizer), array($encoder));

            return JsonResponse::fromJsonString($serializer->serialize($tenRecent, 'json'));
        }

        // string over 100, return empty array.
        return $this->json(array());
    }
}