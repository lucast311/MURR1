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
    public function createRouteFromFile(string $name, $file)
    {

        return array($this->invalidFileFormat, $this->invalidFileType, $this->duplicateSerials);
    }

    /*22a
     * takes in a csv string, returns [a] RoutePickup[s]
     */
    private function CSVToRoutePickup(string $csv)
    {
        
    }

}