<?php

namespace App\Validator\Constraints;

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
class ContactValidator extends ConstraintValidator
{
    public function validate($protocol, Constraint $contraint)
    {
        if(empty($protocol->getRole) && empty($protocol->getFirstName())
            || empty($protocol->getLastName())
            || empty($protocol->getPrimaryPhone())
            || empty($protocol->getSecondaryPhone())
            || empty($protocol->getEmailAddress())
            || empty($protocol->getFax()))
        {
            $this->context->buildViolation($contraint->message)
                ->atPath('/{id}/edit')
                ->addViolation(); 
        }
    }
}