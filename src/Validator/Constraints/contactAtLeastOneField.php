<?php
namespace App\Validator\Constraints;

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
class contactAtLeastOneField extends Constraint
{
    public $messege = 'You must set the role of the contact and at least one other field'; 

    public function validatedBy()
    {
        return get_class($this).'Validator';
    }

    public function getTargets()
    {
        return self::CLASS_CONSTRAINT; 
    }
}