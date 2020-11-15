<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class PhoneValidator extends ConstraintValidator
{
    private $requiredLength = 9;

    public function validate($value, Constraint $constraint)
    {
        if(mb_strlen($value) !== $this->requiredLength) {
            $this->context->buildViolation($constraint->invalidPhoneLength)
                ->setParameter('{{length}}', mb_strlen($value))
                ->setParameter('{{required}}', $this->requiredLength)
                ->addViolation();
        }

        if($value != '' && $value[0] == '0') {
            $this->context->buildViolation($constraint->startsFromZero)
                ->addViolation();
        }
    }
}
