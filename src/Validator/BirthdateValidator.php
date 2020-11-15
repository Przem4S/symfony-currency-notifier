<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class BirthdateValidator extends ConstraintValidator
{
    private $requiredAge = 18;

    public function validate($value, Constraint $constraint)
    {
        if(!$value) {
            $this->context->buildViolation($constraint->invalidDateFormat)
                ->setParameter('{{string}}', (string)$value)
                ->addViolation();
        }

        $current = new \DateTime('now');

        $diff = $current->diff($value);

        if($diff->y < $this->requiredAge) {
            $this->context->buildViolation($constraint->restrictMinimalAge)
                ->setParameter('{{age}}', $diff->y)
                ->setParameter('{{required}}', $this->requiredAge)
                ->addViolation();
        }

        if($diff->y > 100) {
            $this->context->buildViolation($constraint->oldManCongrats)
                ->setParameter('{{age}}', $diff->y)
                ->addViolation();
        }
    }
}
