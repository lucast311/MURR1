<?php
namespace AppBundle\Services;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\EntityManager;

use AppBundle\Entity\Route as ContainerRoute;
use AppBundle\Entity\Route as ContainerRouteTemplate;
use AppBundle\Entity\RoutePickup;

/**
 * TemplateToRoute short summary.
 *
 * TemplateToRoute description.
 *
 * @version 1.0
 * @author cst206
 */
class TemplateToRoute
{

    protected $em;
    public function __construct(ObjectManager $om)
    {
        $this->em = $this->omToEm($om);
    }

    /**
     * S40C
     *
     * Function to take the routePickups from a template and copy them to an empty route
     * @param ContainerRouteTemplate $template
     * @param ContainerRoute $route
     * @return ContainerRoute
     */
    public function templateToRoute($template = null, $route = null)
    {
        $tRoute = null;

        if($template instanceof ContainerRouteTemplate
           && $route instanceof ContainerRoute)
        {
            $rRepo = $this->em->getRepository(ContainerRoute::class);
            $rpRepo = $this->em->getRepository(RoutePickup::class);            
            
            $tRoute = $route;
            if(is_null($tRoute->getId())||$tRoute->getId() > 0) $rRepo->save($tRoute);

            foreach($template->getPickups() as $templatePickup)
            {
                $routePickup = (new RoutePickup())
                    ->setContainer($templatePickup->getContainer())
                    ->setPickupOrder($templatePickup->getPickupOrder())
                    ->setRoute($tRoute);

                $rpRepo->save($routePickup);
                
                $this->em->refresh($tRoute);
            }
        }

        return $tRoute;
    }

    private function omToEm($om): EntityManager
    {
        return $om;
    }
}