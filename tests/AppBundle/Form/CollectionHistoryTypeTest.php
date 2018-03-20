<?php
namespace Tests\AppBundle\Form;

use Symfony\Component\Form\Test\TypeTestCase;
use AppBundle\Entity\CollectionHistory;
use AppBundle\Form\CollectionHistoryType;


/**
 * CollectionHistoryTypeTest short summary.
 *
 * CollectionHistoryTypeTest description.
 *
 * @version 1.0
 * @author Dan
 */
class CollectionHistoryTypeTest extends TypeTestCase
{
    
    /**
     * 18a - test the form works 
     */
    public function testCollectionHistoryForm()
    {
        $formData = array(
          'containerId' => 1,
            'notCollected' => false,
            'notes' => 'Success'
        );

        $form = $this->factory->create(CollectionHistoryType::class, new CollectionHistory());

        $object = new CollectionHistory();

        foreach ($formData as $key=>$value)
        {
            $methodName = "set" . $key;
            $object->$methodName($value);
        }


        //submit the data
        $form->submit($formData);

        //Make sure the from doesen't throw exceptions
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