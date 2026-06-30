<?php
/***************************************************************************
 *                                                                          *
 *   (c) 2004 Vladimir V. Kalynyak, Alexey V. Vinokurov, Ilya M. Shalnev    *
 *                                                                          *
 * This  is  commercial  software,  only  users  who have purchased a valid *
 * license  and  accept  to the terms of the  License Agreement can install *
 * and use this program.                                                    *
 *                                                                          *
 ****************************************************************************
 * PLEASE READ THE FULL TEXT  OF THE SOFTWARE  LICENSE   AGREEMENT  IN  THE *
 * "copyright.txt" FILE PROVIDED WITH THIS DISTRIBUTION PACKAGE.            *
 ****************************************************************************/

namespace Tygh\Addons\StorefrontRestApi\ProfileFields;

use Tygh\Common\OperationResult;
use Tygh\Enum\ProfileFieldTypes;

/**
 * Class Validator validate data against profile fields schema.
 *
 * @package Tygh\Addons\StorefrontRestApi\ProfileFields
 */
class Validator
{
    /**
     * Validates schema fields population.
     *
     * @param array $schema
     * @param array $data
     *
     * @return \Tygh\Common\OperationResult
     */
    public function validate(array $schema, array $data)
    {
        $result = new OperationResult(true);

        $error_fields = [
            'required' => [],
            'invalid'  => [],
        ];

        if (!isset($data['fields'])) {
            $data['fields'] = [];
        }

        foreach ($schema as $section_id => $section) {
            foreach ($section['fields'] as $field_id => $field) {
                if ($field['is_default']) {
                    $value_container = $data;
                } else {
                    $value_container = $data['fields'];
                }

                if ($field['required'] && !isset($value_container[$field_id])) {
                    $result->setSuccess(false);
                    $error_fields['required'][$field_id] = [
                        'is_default' => $field['is_default'],
                        'field_id'   => $field_id,
                    ];
                }

                if (!isset($value_container[$field_id])) {
                    continue;
                }

                if ($field['field_type'] === ProfileFieldTypes::CHECKBOX && !$this->isBool($value_container[$field_id])) {
                    $result->setSuccess(false);
                    $error_fields['invalid'][$field_id] = [
                        'is_default' => $field['is_default'],
                        'field_id'   => $field_id,
                        'value'      => $value_container[$field_id],
                        'values'     => ['true' => 'Y', 'false' => 'N'],
                    ];
                } elseif ($field['field_type'] === ProfileFieldTypes::STATE) {
                    list($is_valid, $values) = $this->validateState($field, $value_container[$field_id]);
                    if (!$is_valid) {
                        $result->setSuccess(false);
                        $error_fields['invalid'][$field_id] = [
                            'is_default' => $field['is_default'],
                            'field_id'   => $field_id,
                            'value'      => $value_container[$field_id],
                            'values'     => $values,
                        ];
                    }
                } elseif (isset($field['values']) && !isset($field['values'][$value_container[$field_id]])) {
                    $result->setSuccess(false);
                    $error_fields['invalid'][$field_id] = [
                        'is_default' => $field['is_default'],
                        'field_id'   => $field_id,
                        'value'      => $value_container[$field_id],
                        'values'     => $field['values'],
                    ];
                }
            }
        }

        $result->setData($error_fields);

        return $result;
    }

    /**
     * Validates state value.
     *
     * @param array  $field
     * @param string $value
     *
     * @return array Validation result and the list of available states
     */
    protected function validateState(array $field, $value)
    {
        if (!$field['values']) {
            return [true, []];
        }

        $valid_values = [];
        foreach ($field['values'] as $country_code => $states) {
            $valid_values = array_merge($valid_values, $states);
            if (isset($states[$value])) {
                return [true, []];
            }
        }

        return [false, $valid_values];
    }

    protected function isBool($value)
    {
        return in_array(
            $value,
            ['N', 'Y', false, true],
            true
        );
    }
}