<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class Phone extends Constraint
{
    public $invalidPhoneLength = 'Phone number length is not valid. Your phone has {{length}} length, {{required}} is required.';
    public $startsFromZero = 'Your phone number starts from zero, it is invalid.';
}
