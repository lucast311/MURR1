<?php
namespace AppBundle\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\BrowserKit\Request;
use AppBundle\Entity\RoutePickup;
use AppBundle\Form\RoutePickupType;
use AppBundle\Entity\Route;

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
     */
    function manageRouteAction(Request $request, $routeId=null){

        $repo = $this->getDoctrine()->getEntityManager()->getRepository(Route::class);
        $route = $repo->findOneById($routeId);

        if($route != null){
            //Create the routePickupForm
            $rp = new RoutePickup();
            $form = $this->createForm(RoutePickupType::class, $rp);

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
     * Story 22b
     * Handles the adding of a routePickup to a route
     */
    function addRoutePickupAction(Request $request){

    }
}