<?php
namespace Tests\AppBundle\Form\Type;

use Symfony\Component\Form\Test\TypeTestCase;
use AppBundle\Entity\Structure;
use AppBundle\Form\StructureType;
/**
 * StructureTypeTest short summary.
 *
 * StructureTypeTest description.
 *
 * @version 1.0
 * @author cst201
 */
class StructureTypeTest extends TypeTestCase
{
    /**
     * 17a - tests the save method in the form
     */
    public function testStructureForm()
    {
        $formData = array(
           key => val
       );

        //create a new form
        $form = $this->factory->create(StructureType::class, new Structure());

        $object = new Structure();
        //populate the new address with the new data
        foreach ($formData as $key=>$value)
        {
            $methodName = "set" . $key;
            $object->$methodName($value);
        }


        //submit the data
        $form->submit($formData);

        //Make sure the from doesent through exceptions
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