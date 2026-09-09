<?php

namespace App\Services;

use Illuminate\Support\Facades\Validator;

class DynamicFormValidator
{
    public static function validate(array $data, array $schema)
    {
        $rules = [];
        $sanitizedData = [];

        foreach ($schema as $field) {
            $name = $field['name'] ?? null;
            if (!$name) continue;

            // Handle Conditional Logic
            if (!empty($field['condition_field'])) {
                $depField = $field['condition_field'];
                $depVal = $data[$depField] ?? null;
                $expectedVal = $field['condition_value'] ?? null;
                
                if ((string)$depVal !== (string)$expectedVal) {
                    // Condition not met (field is hidden on frontend). 
                    // Skip validation and do not include in sanitized data.
                    continue;
                }
            }

            $fieldRules = [];
            
            if (isset($field['required']) && $field['required']) {
                $fieldRules[] = 'required';
            } else {
                $fieldRules[] = 'nullable';
            }

            $type = $field['type'] ?? 'text';
            switch ($type) {
                case 'email':
                    $fieldRules[] = 'email';
                    break;
                case 'number':
                    $fieldRules[] = 'numeric';
                    break;
                case 'date':
                    $fieldRules[] = 'date';
                    break;
                case 'select':
                case 'radio':
                    $fieldRules[] = 'string';
                    if (!empty($field['options'])) {
                        $fieldRules[] = 'in:' . implode(',', $field['options']);
                    }
                    break;
                case 'checkbox':
                    // Checkboxes can be arrays or booleans, relax validation
                    break;
                case 'text':
                default:
                    $fieldRules[] = 'string';
                    break;
            }
            
            $rules[$name] = $fieldRules;
            
            // Sanitization pass (strip HTML tags from string inputs)
            if (array_key_exists($name, $data)) {
                $val = $data[$name];
                if (is_string($val)) {
                    $sanitizedData[$name] = strip_tags($val);
                } else {
                    $sanitizedData[$name] = $val;
                }
            }
        }

        return Validator::make($sanitizedData, $rules);
    }
}
