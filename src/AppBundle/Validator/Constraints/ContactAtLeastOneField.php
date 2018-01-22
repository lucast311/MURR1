<?php
namespace AppBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * contactAtLeastOneField short summary.
 *
 * contactAtLeastOneField description.
 *
 * @version 1.0
 * @author cst201
 */

/**
 * 
 * @Annotation
 */
class ContactAtLeastOneField extends Constraint
{
    public $message = 'You must set the role of the contact and at least one other field'; 

    /**
     * Gets the message
     * @return mixed
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * the class that validates this
     * @return string
     */
    public function validatedBy()
    {
        return get_class($this).'Validator';
    }

    public function getTargets()
    {
        return self::CLASS_CONSTRAINT; 
    }
}