<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 * Validate birthdate format and minimal required age.
 */
class Birthdate extends Constraint
{
    public $invalidDateFormat = 'Invalid date format. Accepted format is only YYYY-MM-DD.';
    public $restrictMinimalAge = 'You have only {{age}} years old. {{required}} years is required.';
    public $oldManCongrats = 'You are really old! Your age {{age}} years is very impressive but.. I don\'t believe you ;)';
}
