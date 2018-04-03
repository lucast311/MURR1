<?php
namespace Tests\AppBundle\Form;

use Symfony\Component\Form\Test\TypeTestCase;
use AppBundle\Entity\Container;
use AppBundle\Form\ContainerType;
use AppBundle\Form\ContainerEditType;

/**
 * ContainerTypeTest short summary.
 *
 * ContainerTypeTest description.
 *
 * @version 1.0
 * @author Dan
 */
class ContainerTypeTest extends TypeTestCase
{
    
    
    public function testContainerForm()
    {
        $formData = array(
           'containerSerial' => 'testSerial',
           'type' => 'Bin',
           'size' => '6',
           'status' => 'Active',
           'augmentation' => 'wheels'
       );

        //create a new form
        $form = $this->factory->create(ContainerType::class, new Container());

        $object = new Container();
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

    //public function testEditContainerForm()
    //{
    //    $formData = array(
    //       'type' => 'Bin',
    //       'size' => '6',
    //       'status' => 'Active',
    //       'augmentation' => 'wheels'
    //   );

    //    //create a new form
    //    $form = $this->factory->create(ContainerEditType::class, new Container());

    //    $object = new Container();
    //    //populate the new address with the new data
    //    foreach ($formData as $key=>$value)
    //    {
    //        $methodName = "set" . $key;
    //        $object->$methodName($value);
    //    }


    //    //submit the data
    //    $form->submit($formData);

    //    //Make sure the from doesent through exceptions
    //    $this->assertTrue($form->isSynchronized());
    //    //Check that the from contains the objects info.
    //    $this->assertEquals($object,$form->getData());
    //    //create the forms view
    //    $view = $form->createView();
    //    //get the children of the form
    //    $children = $view->children;
    //    //make sure the form has all the right fields
    //    foreach ($formData as $key=>$value)
    //    {
    //        $this->assertArrayHasKey($key,$children);
    //    }
    //}
}