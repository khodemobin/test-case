<?php

namespace App\Validation\Attributes;

use Attribute;

#[Attribute(Attribute::TARGET_PROPERTY)]
class Required
{
    public function __construct(public string $message = 'This field is required') {}
}