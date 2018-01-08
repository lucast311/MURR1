
<?php
/*

namespace Tests\AppBundle\Form;
use AppBundle\Entity\OOPs;

use OOPsType;
use AppBundle\Model\TestObject;
use Symfony\Component\Form\Test\TypeTestCase;
*/
/**
 * CSROOPsFormTest short summary.
 *
 * CSROOPsFormTest description.
 *
 * @version 1.0
 * @author cst201
 */
/*
class CSROOPsFormTest extends TypeTestCase
{
    public function testSubmitValidData()
    {


        //^ this is based off of the testAddActionSuccess() method in ContactControllerTest.php in master
        //  INCOMPLETE

        /*
        $validImageFile = '../../../app/Resources/images/OOPs NOTICE Valid1.png';
        $validImage = readfile($validImageFile);
        */
/*
        $formData = array(
            'binSerial' => '1111111111',
            'problemType' => "Damage",
            'description' => "Someone filled it with dog turds"
        );

        //create a new form and verify it compiles
        $oopsForm = $this->factory->create(OOPsType::class, new OOPs('',''));

        //create a new object with some data
        $object = new OOPs("1111111111","damage");
        foreach($formData as $key=>$value)
        {
            $object->__set($key,$value);
        }

        //submit the data to the form directly
        $oopsForm->submit($formData);

        //this makes sure the form doesn't throw exeptions and whatnot
        $this->assertTrue($oopsForm->isSynchronized());
        //check if the form contains the objects information
        $this->assertEquals($object, $oopsForm->getData());
        //create the forms view
        $view = $oopsForm->createView();
        //get the children of the form
        $children = $view->children;



        foreach (array_keys($formData) as $key) {
            //make sure the form has each key
            $this->assertArrayHasKey($key, $children);
        }

    }
}
*/