<?php

namespace App\Validation\Attributes;

use Attribute;

#[Attribute(Attribute::TARGET_PROPERTY)]
class PositiveNumber
{
    public function __construct(public string $message = 'This field must be a positive number') {}
}