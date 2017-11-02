<?php


namespace Tests\AppBundle\Form;

use AppBundle\Form\Type\TestedType;
use AppBundle\Model\TestObject;
use Symfony\Component\Form\Test\TypeTestCase;
use AppBundle\Form\OOPsType;
use AppBundle\Entity\OOPs;
use AppBundle\Form\AddOOPsForm;
/**
 * CSROOPsFormTest short summary.
 *
 * CSROOPsFormTest description.
 *
 * @version 1.0
 * @author cst201
 */
class CSROOPsFormTest extends TypeTestCase
{
    public function testSubmitValidData()
    {


        //^ this is based off of the testAddActionSuccess() method in ContactControllerTest.php in master
        //  INCOMPLETE

        
        $validImageFile = '../../../app/Resources/images/OOPs NOTICE Valid1.png';
        $validImage = readfile($validImageFile);

        $formData = array(
            'binSerial' => '1111111111',
            'problemType' => "Damage",
            'description' => "Someone filled it with dog turds",
            'image' => $validImage
        );

        //create a new form and verify it compiles
        $form = $this->factory->create(OOPsType::class, new OOPs("1111111111","damage"));

        //create a new object with some data
        $object = new OOPs("1111111111","damage");
        foreach($formData as $key=>$value)
        {
            $object->__set($key,$value);
        }

        //submit the data to the form directly
        $form->submit($formData);

        //this makes sure the form doesn't throw exeptions and whatnot
        $this->assertTrue($form->isSynchronized());
        //check if the form contains the objects information
        $this->assertEquals($object, $form->getData());
        //create the forms view
        $view = $form->createView();
        //get the children of the form
        $children = $view->children;



        foreach (array_keys($formData) as $key) {
            //make sure the form has each key
            $this->assertArrayHasKey($key, $children);
        }
        
    }
}