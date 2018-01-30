<?php
use Symfony\Component\Form\Test\TypeTestCase;
use AppBundle\Entity\RoutePickup;
use AppBundle\Entity\Container;
use AppBundle\Entity\Route;

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
     * Story 22b
     * Tests that the form class can be created and submit data
     */
    public function testSubmitData()
    {
        // The data that will be "submitted" to the form
        $formData = array(
            'pickupOrder' => 51,
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

        // New container and routes need to be create for the routePickup
        $object->setContainer(new Container());
        $object->setRoute(new Route());

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