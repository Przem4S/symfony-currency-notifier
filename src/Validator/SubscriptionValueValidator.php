<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class SubscriptionValueValidator extends ConstraintValidator
{
    public function validate($value, Constraint $constraint)
    {
        if($value <= 0) {
            $this->context->buildViolation($constraint->valueBelowZero)
                ->addViolation();
        }
    }
}
