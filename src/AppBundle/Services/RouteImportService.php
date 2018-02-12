<?php
namespace AppBundle\Services;

use AppBundle\Entity\Container;
use AppBundle\Entity\Route;
use AppBundle\Entity\RoutePickup;
use AppBundle\Repository\RouteRepository;
use AppBundle\Repository\RoutePickupRepository;

/**
 * Story22a
 * This class takes a name and a csv file from the RouteController and creates a route from them
 */
class RouteImportService
{
    private $invalidFileFormat;
    private $invalidFileType;
    private $duplicateSerials;
    private $route;

    /*22a
     * takes in a name and file, attempts to create a route
     */
    public function createRouteFromFile(int $routeId, $file)
    {
        $em = $this->getDoctrine()->getManager();

        $this->invalidFileFormat = false;
        $this->invalidFileType   = false;
        $this->duplicateSerials  = false;
        $this->route = (new Route())->setRouteId($routeId);

        $csvString; //read from file

        $routePickups = $this->csvToRoutePickup($csvString);

        //Get the repository for testing
        $routePickupRepo = $em->getRepository(RoutePickup::class);

        if(!$this->invalidFileFormat)
        {
            foreach($routePickups as $routePickup)
            {
                //Call insert on the repository and record the id of the new object
                $routePickupRepo->save($routePickup);
            }
        }

        return array($this->invalidFileFormat, $this->invalidFileType, $this->duplicateSerials);
    }

    /*22a
     * takes in a csv string, returns [a] RoutePickup[s]
     */
    private function csvToRoutePickup(string $csv)
    {
        $usedContainerIds = array();


    }

}