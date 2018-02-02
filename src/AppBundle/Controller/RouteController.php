<?php
namespace AppBundle\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use AppBundle\Entity\RoutePickup;
use AppBundle\Form\RoutePickupType;
use AppBundle\Entity\Route as ContainerRoute;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Form\FormError;

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
 */
class RouteController extends Controller
{
    /**
     * Story 22b
     * Brings you to the manage route page
     * @Route("/route/{routeId}", name="route_manage")
     * @param Request $request
     * @param integer $routeId
     */
    function manageRouteAction(Request $request, $routeId=null){

        $em = $this->getDoctrine()->getEntityManager();

        //NOTE THE USE OF THE USING STATEMENT TO MAKE CONTAINER ROUTE A SHORTCUT TO ROUTE
        //Otherwise there is conflicts between the route annotations and the route class
        $routeRepo = $em->getRepository(ContainerRoute::class);
        $route = $routeRepo->findOneById($routeId);

        if($route != null){
            //Create the routePickupForm
            $rp = new RoutePickup();
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

                    $repo = $this->getDoctrine()->getEntityManager()->getRepository(RoutePickup::class);

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
                    }
                    //set this pickup on the current route
                    $rp->setRoute($route);
                    $repo->save($rp);

                    //refresh the route to display the new data
                    //And since the pickups are set to cascade refresh it will reload them too
                    $em->refresh($route);
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

    ///**
    // * Story 22b
    // * Handles the adding of a routePickup to a route
    // */
    //function addRoutePickupAction(RoutePickup $rp){


    //    return $this->render('route/manageRoute.html.twig',
    //        array('form'=>$form->createView(),
    //        'route'=>$route,
    //        'invalid_id_error'=>false));
    //}
}