<?php
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * ContactAddPropertyType short summary.
 *
 * ContactAddPropertyType description.
 *
 * @version 1.0
 * @author cst244
 */
class ContactAddPropertyType extends AbstractType
{
    /**
     * Builds the form for associating a property to a contact
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        
    }

    /**
     * Sets the options for this class, use the class of Contact
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        
    }

    /**
     * Story 4k
     * Sets the form name for this class
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        
    }


}
