<?php
namespace Tests\AppBundle\Form\Type;

use Symfony\Component\Form\Test\TypeTestCase;
use AppBundle\Form\PropertyType;
use AppBundle\Entity\Property;
use AppBundle\Entity\Address;
use Symfony\Component\Validator\Validation;
use Symfony\Component\Form\Extension\Validator\ValidatorExtension;
class PropertyTypeTest extends TypeTestCase
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
     * Tests that the form class can be created and submit data
     */
    public function testSubmitData()
    {
        // The data that will be "submitted" to the form
        $formData = array(
            'id' => 5555555,
            'propertyName' => 'Cosmo House',
            'propertyType' => 'Townhouse Condo',
            'propertyStatus' => 'Active',
            'structureId' => 9999,
            'numUnits' => 12,
            'neighbourhoodName' => 'Sutherland',
            'neighbourhoodId' => 'O33'
        );

        //create a new form
        $form = $this->factory->create(PropertyType::class, new Property());

        $object = new Property();
        //populate the new property with the new data
        foreach ($formData as $key=>$value)
        {
            $methodName = "set" . $key;
            $object->$methodName($value);
        }

        //submit the data
        $form->submit($formData);

        // New address has to be created to match the new address doctrine would have created for us in the form.
        $object->setAddress(new Address());

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