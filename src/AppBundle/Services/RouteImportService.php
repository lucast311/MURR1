<?php

namespace AppBundle\Services;

use AppBundle\Entity\Container;
use AppBundle\Entity\Route;
use AppBundle\Entity\RoutePickup;
use AppBundle\Repository\RouteRepository;
use AppBundle\Repository\RoutePickupRepository;

/**
 * This class takes a name and a csv file from the RouteController and creates a route from them
 */
class RouteImportService
{
    private $invalidFileFormat;
    private $invalidFileType;
    private $duplicateSerials;
    private $route;

}