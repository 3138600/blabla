<?php

namespace Tygh\Tests\Unit\Addons\StorefrontRestApi\ProfileFields;

use Tygh\Addons\StorefrontRestApi\ProfileFields\Validator;
use Tygh\Tests\Unit\ATestCase;

class ValidatorTest extends ATestCase
{
    public $runTestInSeparateProcess = true;

    public $backupGlobals = false;

    public $preserveGlobalState = false;

    protected $schema;

    /**
     * @param array|null $schema
     * @param array      $data
     * @param bool       $expected_success
     * @param array      $expected_data
     *
     * @dataProvider getTestValidate
     */
    public function testValidate($schema, $data, $expected_success, $expected_data)
    {
        $validator = new Validator();
        if ($schema === null) {
            $schema = $this->schema;
        }

        $actual = $validator->validate($schema, $data);

        $this->assertEquals($expected_success, $actual->isSuccess());
        $this->assertEquals($expected_data, $actual->getData());
    }

    public function getTestValidate()
    {
        return [
            // empty schema and request
            [
                [],
                [],
                true,
                ['required' => [], 'invalid' => []],
            ],

            // default + custom fields
            [
                null,
                [
                    'email'       => 'user@example.com',
                    'b_firstname' => 'Firstname',
                    's_firstname' => 'Firstname',
                    'b_lastname'  => 'Lastname',
                    's_lastname'  => 'Lastname',
                    'b_phone'     => 'Phone',
                    's_phone'     => 'Phone',
                    'b_address'   => 'Address',
                    's_address'   => 'Address',
                    'b_address_2' => 'Address 2',
                    's_address_2' => 'Address 2',
                    'b_city'      => 'City',
                    's_city'      => 'City',
                    'b_country'   => 'US',
                    's_country'   => 'US',
                    'b_state'     => 'AK',
                    's_state'     => 'AK',
                    'b_zipcode'   => '12345',
                    's_zipcode'   => '12345',
                    'fields'      => [
                        '50' => '1',
                        '51' => 'FOOBAR',
                        '52' => 'FOOBAR',
                    ],
                ],
                true,
                ['required' => [], 'invalid' => []],
            ],

            // required fields
            [
                null,
                [
                    'email'       => 'user@example.com',
                    's_firstname' => 'Firstname',
                    's_lastname'  => 'Lastname',
                    's_phone'     => 'Phone',
                    's_address'   => 'Address',
                    's_address_2' => 'Address 2',
                    's_city'      => 'City',
                    's_country'   => 'US',
                    's_state'     => 'AK',
                    's_zipcode'   => '12345',
                    'fields'      => [
                        '51' => 'FOOBAR',
                        '52' => 'FOOBAR',
                    ],
                ],
                true,
                ['required' => [], 'invalid' => []],
            ],

            // required field - default
            [
                null,
                [
                    's_firstname' => 'Firstname',
                    's_lastname'  => 'Lastname',
                    's_phone'     => 'Phone',
                    's_address'   => 'Address',
                    's_address_2' => 'Address 2',
                    's_city'      => 'City',
                    's_country'   => 'US',
                    's_state'     => 'AK',
                    's_zipcode'   => '12345',
                    'fields'      => [
                        '51' => 'FOOBAR',
                        '52' => 'FOOBAR',
                    ],
                ],
                false,
                [
                    'required' => [
                        'email' => [
                            'is_default' => true,
                            'field_id'   => 'email',
                        ],
                    ],
                    'invalid'  => [],
                ],
            ],

            // required field - custom
            [
                null,
                [
                    'email'       => 'user@example.com',
                    's_firstname' => 'Firstname',
                    's_lastname'  => 'Lastname',
                    's_phone'     => 'Phone',
                    's_address'   => 'Address',
                    's_address_2' => 'Address 2',
                    's_city'      => 'City',
                    's_country'   => 'US',
                    's_state'     => 'AK',
                    's_zipcode'   => '12345',
                    'fields'      => [
                        '52' => 'FOOBAR',
                    ],
                ],
                false,
                [
                    'required' => [
                        '51' => [
                            'is_default' => false,
                            'field_id'   => '51',
                        ],
                    ],
                    'invalid'  => [],
                ],
            ],

            // invalid field - default
            [
                null,
                [
                    'email'       => 'user@example.com',
                    's_firstname' => 'Firstname',
                    's_lastname'  => 'Lastname',
                    's_phone'     => 'Phone',
                    's_address'   => 'Address',
                    's_address_2' => 'Address 2',
                    's_city'      => 'City',
                    's_country'   => 'MISSING_COUNTRY',
                    's_state'     => 'AK',
                    's_zipcode'   => '12345',
                    'fields'      => [
                        '50' => '1',
                        '51' => 'FOOBAR',
                        '52' => 'FOOBAR',
                    ],
                ],
                false,
                [
                    'required' => [],
                    'invalid'  => [
                        's_country' => [
                            'is_default' => true,
                            'field_id'   => 's_country',
                            'value'      => 'MISSING_COUNTRY',
                            'values'     => [
                                'NL' => 'Netherlands',
                                'US' => 'United States',
                            ],
                        ],
                    ],
                ],
            ],

            // invalid field - custom
            [
                null,
                [
                    'email'       => 'user@example.com',
                    'b_firstname' => 'Firstname',
                    's_firstname' => 'Firstname',
                    'b_lastname'  => 'Lastname',
                    's_lastname'  => 'Lastname',
                    'b_phone'     => 'Phone',
                    's_phone'     => 'Phone',
                    'b_address'   => 'Address',
                    's_address'   => 'Address',
                    'b_address_2' => 'Address 2',
                    's_address_2' => 'Address 2',
                    'b_city'      => 'City',
                    's_city'      => 'City',
                    'b_country'   => 'US',
                    's_country'   => 'US',
                    'b_state'     => 'AK',
                    's_state'     => 'AK',
                    'b_zipcode'   => '12345',
                    's_zipcode'   => '12345',
                    'fields'      => [
                        '42' => 'bazinga!',
                        '50' => '5',
                        '51' => 'FOOBAR',
                        '52' => 'FOOBAR',
                    ],
                ],
                false,
                [
                    'required' => [],
                    'invalid'  => [
                        '42' => [
                            'is_default' => false,
                            'field_id'   => 42,
                            'value'      => 'bazinga!',
                            'values' => [
                                'true'  => 'Y',
                                'false' => 'N',
                            ],
                        ],
                        '50' => [
                            'is_default' => false,
                            'field_id'   => '50',
                            'value'      => '5',
                            'values'     => [
                                '1' => 'Foo',
                                '2' => 'Bar',
                                '3' => 'Baz',
                            ],
                        ],
                    ],
                ],
            ],
        ];
    }

    protected function setUp()
    {
        $this->schema = json_decode(file_get_contents(__DIR__ . '/data/schema.json'), true);
    }
}