<?php

namespace Tests\AppBundle\Form\Type;

use Symfony\Component\Form\Test\TypeTestCase;
use Symfony\Component\Validator\Validation;
use Symfony\Component\Form\Extension\Validator\ValidatorExtension;
use AppBundle\Entity\Property;
use AppBundle\Entity\Address;
use AppBundle\Entity\Contact;
use AppBundle\Form\ContactAddPropertyType;

/**
 * ContactAddPropertyTypeTest short summary.
 *
 * ContactAddPropertyTypeTest description.
 *
 * @version 1.0
 * @author cst244
 */
class ContactAddPropertyTypeTest extends TypeTestCase
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
     * Story 4k
     * Tests that a form can be created and can be submitted
     */
    public function testSubmitData()
    {
        //specify a property for the form data
        $property = (new Property())
           ->setSiteId(333666999)
           ->setPropertyName("Balla Highrize")
           ->setNumUnits(102)
           ->setPropertyStatus("Active")
           ->setPropertyType("High Rise Apartment")
           ->setNeighbourhoodName("Compton")
           ->setAddress((new Address())
               ->setStreetAddress("456 West Street")
               ->setCity("Compton")
               ->setCountry("America")
               ->setPostalCode("A1A 1A1")
               ->setProvince("CA"));

        //specify a contact for the form data
        $contact = (new Contact())
            ->setFirstName("Bill")
            ->setLastName("Smith")
            ->setPrimaryPhone("123-321-6439")
            ->setRole("Property Manager")
            ->setPhoneExtension(123)
            ->setEmailAddress("billsmith@email.com");

        // The data that will be "submitted" to the form
        $formData = array(
            'property' => $property,
            'contact'=>$contact
        );

        //create a new form
        $form = $this->factory->create(ContactAddPropertyType::class, null,array('contact'=>1));

        $form->setData($formData);

        //submit the data
        $form->submit($formData);


        //Make sure the from doesent throw exceptions
        $this->assertTrue($form->isSynchronized());

        //Check that the form contains the right data
        $this->assertEquals($formData,$form->getData());

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