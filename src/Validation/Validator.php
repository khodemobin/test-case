<?php

namespace App\Validation;

use App\Views\JsonResponse;
use ReflectionClass;
use ReflectionProperty;

class Validator
{
    public function __construct(private JsonResponse $view) {}

    public function validate(object $requestData, bool $exit = true): bool
    {
        $isValid = true;
        $reflection = new ReflectionClass($requestData);
        foreach ($reflection->getProperties() as $property) {
            if (!$this->validateProperty($property, $requestData, $exit)) {
                $isValid = false;
            }
        }
        return $isValid;
    }

    private function validateProperty(ReflectionProperty $property, object $requestData, bool $exit): bool
    {
        $value = $property->isInitialized($requestData) ? $property->getValue($requestData) : null;
        $attributes = $property->getAttributes();
        $isValid = true;

        foreach ($attributes as $attribute) {
            $instance = $attribute->newInstance();
            switch (get_class($instance)) {
                case Attributes\Required::class:
                    if ($value === null || $value === '') {
                        $this->view->error($instance->message, 400, $exit);
                        $isValid = false;
                    }
                    break;
                case Attributes\StringType::class:
                    if ($value !== null && !is_string($value)) {
                        $this->view->error($instance->message, 400, $exit);
                        $isValid = false;
                    }
                    break;
                case Attributes\PositiveNumber::class:
                    if ($value !== null && (!is_numeric($value) || $value <= 0)) {
                        $this->view->error($instance->message, 400, $exit);
                        $isValid = false;
                    }
                    break;
            }
        }
        return $isValid;
    }
}