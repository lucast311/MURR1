<?php
namespace Tests\AppBundle\Form;

use Symfony\Component\Form\Test\TypeTestCase;
use AppBundle\Entity\RoutePickup;
use AppBundle\Entity\Container;
use AppBundle\Entity\Route;
use AppBundle\Form\RoutePickupType;
use Symfony\Component\Validator\Validation;
use Symfony\Component\Form\Extension\Validator\ValidatorExtension;

/**
 * RoutePickupTypeTest short summary.
 *
 * RoutePickupTypeTest description.
 *
 * @version 1.0
 * @author cst244
 */
class RoutePickupTypeTest extends TypeTestCase
{

    /**
     * This method is required to allow the test to run
     * @return ValidatorExtension[]
     */
    protected function getExtensions()
    {
        return array(new ValidatorExtension(Validation::createValidator()));
    }

    /**
     * Story 22b
     * Tests that the form class can be created and submit data
     */
    public function testSubmitData()
    {
        //create a route for the form data
        //$route = new Route();
        //$route->setRouteId(1001);

        //specify a container for the form data
        $container = new Container();
        $container->setContainerSerial("X11111");
        $container->setType("Bin");
        $container->setSize("6");
        $container->setStatus("Active");

        // The data that will be "submitted" to the form
        $formData = array(
            'pickupOrder' => 1,
            'container'=>$container
        );

        //create a new form
        $form = $this->factory->create(RoutePickupType::class, new RoutePickup());

        $object = new RoutePickup();
        //populate the new property with the new data
        foreach ($formData as $key=>$value)
        {
            $methodName = "set" . $key;
            $object->$methodName($value);
        }

        //submit the data
        $form->submit($formData);

        //// New container and routes need to be create for the routePickup
        //$object->setContainer(new Container());
        //$object->setRoute(new Route());

        //Make sure the from doesent throw exceptions
        $this->assertTrue($form->isSynchronized());
        //Check that the from contains the objects info.
        $this->assertEquals($object,$form->getData());
        //create the forms view
        $view = $form->createView();
        //get the children of the form
        $children = $view->children;
        //make sure the form has all the right fields
        foreach ($formData as $key=>$value)
        {
            $this->assertArrayHasKey($key,$children);
        }

    }
}