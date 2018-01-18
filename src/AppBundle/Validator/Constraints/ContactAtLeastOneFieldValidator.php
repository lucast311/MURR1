<?php

namespace AppBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
/**
 * contactValidator short summary.
 *
 * contactValidator description.
 *
 * @version 1.0
 * @author cst201
 */
class ContactAtLeastOneFieldValidator extends ConstraintValidator
{
    /**
     * Custom validator to ensure that a contact role and some other contact information 
     * has been posted
     * @param mixed $protocol - what called this validator 
     * @param Constraint $contraint - the custom contraint 
     */
    public function validate($protocol, Constraint $contraint)
    {
        // If the role is empty and all other roles are empty
        if(empty($protocol->getRole) &&
            ( empty($protocol->getFirstName())
            && empty($protocol->getLastName())
            && empty($protocol->getPrimaryPhone())
            && empty($protocol->getSecondaryPhone())
            && empty($protocol->getEmailAddress())
            && empty($protocol->getFax())) )
        {         
            
            $this->context->buildViolation($contraint->getMessage())
                ->atPath('AppBundle::Contact')
                ->addViolation();
        }
    }
}