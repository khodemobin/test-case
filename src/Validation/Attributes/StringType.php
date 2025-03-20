<?php

namespace App\Validation\Attributes;

use Attribute;

#[Attribute(Attribute::TARGET_PROPERTY)]
class StringType
{
    public function __construct(public string $message = 'This field must be a string') {}
}