<?php

namespace Tygh\Tests\Unit\Addons\StorefrontRestApi\ProfileFields;

use Tygh\Addons\StorefrontRestApi\ProfileFields\Hydrator;
use Tygh\Tests\Unit\ATestCase;

class HydratorTest extends ATestCase
{
    public $runTestInSeparateProcess = true;

    public $backupGlobals = false;

    public $preserveGlobalState = false;

    protected $schema;

    /**
     * @param array|null $schema
     * @param array      $data
     * @param array      $expected
     *
     * @dataProvider getTestHydrate
     */
    public function testHydrate($schema, $data, $expected)
    {
        $hydrator = new Hydrator();
        if ($schema === null) {
            $schema = $this->schema;
        }

        $actual = $hydrator->hydrate($schema, $data);

        $this->assertEquals($expected, $actual);
    }

    public function getTestHydrate()
    {
        return [
            [
                [],
                [],
                [],
            ],

            // non-hydrated
            [
                null,
                [],
                [
                    'E' => [
                        'description' => '',
                        'fields'      => [
                            'email'     => [
                                'field_type'  => 'I',
                                'field_name'  => 'email',
                                'description' => 'E-mail',
                                'is_default'  => true,
                                'required'    => true,
                                'value'       => null,
                            ],
                            'password1' => [
                                'field_type'  => 'W',
                                'field_name'  => 'password1',
                                'description' => 'Password',
                                'required'    => false,
                                'is_default'  => true,
                                'value'       => null,
                            ],
                            'password2' => [
                                'field_type'  => 'W',
                                'field_name'  => 'password2',
                                'description' => 'Confirm password',
                                'required'    => false,
                                'is_default'  => true,
                                'value'       => null,
                            ],
                        ],
                    ],
                    'C' => [
                        'description' => 'Contact information',
                        'fields'      => [
                            42 => [
                                'field_id'    => '42',
                                'field_name'  => 'bar',
                                'field_type'  => 'C',
                                'is_default'  => false,
                                'description' => 'Bar',
                                'required'    => false,
                                'value'       => null,
                            ],
                            50 => [
                                'field_id'    => '50',
                                'field_name'  => 'foo',
                                'field_type'  => 'S',
                                'is_default'  => false,
                                'description' => 'Foo',
                                'required'    => false,
                                'values'      => [
                                    1 => 'Foo',
                                    2 => 'Bar',
                                    3 => 'Baz',
                                ],
                                'value'       => null,
                            ],
                        ],
                    ],
                    'B' => [
                        'description' => 'Billing address',
                        'fields'      => [
                            51            => [
                                'field_id'    => '51',
                                'field_name'  => 'b_bar',
                                'field_type'  => 'I',
                                'is_default'  => false,
                                'description' => 'Bar',
                                'required'    => true,
                                'value'       => null,
                            ],
                            'b_firstname' => [
                                'field_id'    => '14',
                                'field_name'  => 'b_firstname',
                                'field_type'  => 'I',
                                'is_default'  => true,
                                'description' => 'First name',
                                'required'    => false,
                                'value'       => null,
                            ],
                            'b_lastname'  => [
                                'field_id'    => '16',
                                'field_name'  => 'b_lastname',
                                'field_type'  => 'I',
                                'is_default'  => true,
                                'description' => 'Last name',
                                'required'    => false,
                                'value'       => null,
                            ],
                            'email'       => [
                                'field_id'    => '32',
                                'field_name'  => 'email',
                                'field_type'  => 'E',
                                'is_default'  => true,
                                'description' => 'E-mail',
                                'required'    => true,
                                'value'       => null,
                            ],
                            'b_phone'     => [
                                'field_id'    => '30',
                                'field_name'  => 'b_phone',
                                'field_type'  => 'I',
                                'is_default'  => true,
                                'description' => 'Phone',
                                'required'    => false,
                                'value'       => null,
                            ],
                            'b_address'   => [
                                'field_id'    => '18',
                                'field_name'  => 'b_address',
                                'field_type'  => 'I',
                                'is_default'  => true,
                                'description' => 'Address',
                                'required'    => false,
                                'value'       => null,
                            ],
                            'b_address_2' => [
                                'field_id'    => '20',
                                'field_name'  => 'b_address_2',
                                'field_type'  => 'I',
                                'is_default'  => true,
                                'description' => 'Address',
                                'required'    => false,
                                'value'       => null,
                            ],
                            'b_city'      => [
                                'field_id'    => '22',
                                'field_name'  => 'b_city',
                                'field_type'  => 'I',
                                'is_default'  => true,
                                'description' => 'City',
                                'required'    => false,
                                'value'       => null,
                            ],
                            'b_country'   => [
                                'field_id'    => '26',
                                'field_name'  => 'b_country',
                                'field_type'  => 'O',
                                'is_default'  => true,
                                'description' => 'Country',
                                'required'    => false,
                                'values'      => [
                                    'NL' => 'Netherlands',
                                    'US' => 'United States',
                                ],
                                'value'       => null,
                            ],
                            'b_state'     => [
                                'field_id'    => '24',
                                'field_name'  => 'b_state',
                                'field_type'  => 'A',
                                'is_default'  => true,
                                'description' => 'State/province',
                                'required'    => false,
                                'values'      => [
                                    'NL' => [
                                        'DR' => 'Drenthe',
                                        'FL' => 'Flevoland',
                                        'FR' => 'Friesland',
                                    ],
                                    'US' => [
                                        'AL' => 'Alabama',
                                        'AK' => 'Alaska',
                                        'AZ' => 'Arizona',
                                    ],
                                ],
                                'value'       => null,
                            ],
                            'b_zipcode'   => [
                                'field_id'    => '28',
                                'field_name'  => 'b_zipcode',
                                'field_type'  => 'Z',
                                'is_default'  => true,
                                'description' => 'Zip/postal code',
                                'required'    => false,
                                'value'       => null,
                            ],
                        ],
                    ],
                    'S' => [
                        'description' => 'Shipping address',
                        'fields'      => [
                            52            => [
                                'field_id'    => '52',
                                'field_name'  => 's_bar',
                                'field_type'  => 'I',
                                'is_default'  => false,
                                'description' => 'Bar',
                                'required'    => true,
                                'value'       => null,
                            ],
                            's_firstname' => [
                                'field_id'    => '15',
                                'field_name'  => 's_firstname',
                                'field_type'  => 'I',
                                'is_default'  => true,
                                'description' => 'First name',
                                'required'    => false,
                                'value'       => null,
                            ],
                            's_lastname'  => [
                                'field_id'    => '17',
                                'field_name'  => 's_lastname',
                                'field_type'  => 'I',
                                'is_default'  => true,
                                'description' => 'Last name',
                                'required'    => false,
                                'value'       => null,
                            ],
                            's_phone'     => [
                                'field_id'    => '31',
                                'field_name'  => 's_phone',
                                'field_type'  => 'I',
                                'is_default'  => true,
                                'description' => 'Phone',
                                'required'    => false,
                                'value'       => null,
                            ],
                            's_address'   => [
                                'field_id'    => '19',
                                'field_name'  => 's_address',
                                'field_type'  => 'I',
                                'is_default'  => true,
                                'description' => 'Address',
                                'required'    => false,
                                'value'       => null,
                            ],
                            's_address_2' => [
                                'field_id'    => '21',
                                'field_name'  => 's_address_2',
                                'field_type'  => 'I',
                                'is_default'  => true,
                                'description' => 'Address',
                                'required'    => false,
                                'value'       => null,
                            ],
                            's_city'      => [
                                'field_id'    => '23',
                                'field_name'  => 's_city',
                                'field_type'  => 'I',
                                'is_default'  => true,
                                'description' => 'City',
                                'required'    => false,
                                'value'       => null,
                            ],
                            's_country'   => [
                                'field_id'    => '27',
                                'field_name'  => 's_country',
                                'field_type'  => 'O',
                                'is_default'  => true,
                                'description' => 'Country',
                                'required'    => false,
                                'values'      => [
                                    'NL' => 'Netherlands',
                                    'US' => 'United States',
                                ],
                                'value'       => null,
                            ],
                            's_state'     => [
                                'field_id'    => '25',
                                'field_name'  => 's_state',
                                'field_type'  => 'A',
                                'is_default'  => true,
                                'description' => 'State/province',
                                'required'    => false,
                                'values'      => [
                                    'NL' => [
                                        'DR' => 'Drenthe',
                                        'FL' => 'Flevoland',
                                        'FR' => 'Friesland',
                                    ],
                                    'US' => [
                                        'AL' => 'Alabama',
                                        'AK' => 'Alaska',
                                        'AZ' => 'Arizona',
                                    ],
                                ],
                                'value'       => null,
                            ],
                            's_zipcode'   => [
                                'field_id'    => '29',
                                'field_name'  => 's_zipcode',
                                'field_type'  => 'Z',
                                'is_default'  => true,
                                'description' => 'Zip/postal code',
                                'required'    => false,
                                'value'       => null,
                            ],
                        ],
                    ],
                ],
            ],

            // hydrated
            [
                null,
                [
                    'email'  => 'user@example.com',
                    'fields' => [
                        42 => 'Y',
                        51 => 2,
                        52 => 'FOOBAR',
                    ],
                ],
                [
                    'E' => [
                        'description' => '',
                        'fields'      => [
                            'email'     => [
                                'field_type'  => 'I',
                                'field_name'  => 'email',
                                'description' => 'E-mail',
                                'is_default'  => true,
                                'required'    => true,
                                'value'       => 'user@example.com',
                            ],
                            'password1' => [
                                'field_type'  => 'W',
                                'field_name'  => 'password1',
                                'description' => 'Password',
                                'required'    => false,
                                'is_default'  => true,
                                'value'       => null,
                            ],
                            'password2' => [
                                'field_type'  => 'W',
                                'field_name'  => 'password2',
                                'description' => 'Confirm password',
                                'required'    => false,
                                'is_default'  => true,
                                'value'       => null,
                            ],
                        ],
                    ],
                    'C' => [
                        'description' => 'Contact information',
                        'fields'      => [
                            42 => [
                                'field_id'    => '42',
                                'field_name'  => 'bar',
                                'field_type'  => 'C',
                                'is_default'  => false,
                                'description' => 'Bar',
                                'required'    => false,
                                'value'       => true,
                            ],
                            50 => [
                                'field_id'    => '50',
                                'field_name'  => 'foo',
                                'field_type'  => 'S',
                                'is_default'  => false,
                                'description' => 'Foo',
                                'required'    => false,
                                'values'      => [
                                    1 => 'Foo',
                                    2 => 'Bar',
                                    3 => 'Baz',
                                ],
                                'value'       => null,
                            ],
                        ],
                    ],
                    'B' => [
                        'description' => 'Billing address',
                        'fields'      => [
                            51            => [
                                'field_id'    => '51',
                                'field_name'  => 'b_bar',
                                'field_type'  => 'I',
                                'is_default'  => false,
                                'description' => 'Bar',
                                'required'    => true,
                                'value'       => 2,
                            ],
                            'b_firstname' => [
                                'field_id'    => '14',
                                'field_name'  => 'b_firstname',
                                'field_type'  => 'I',
                                'is_default'  => true,
                                'description' => 'First name',
                                'required'    => false,
                                'value'       => null,
                            ],
                            'b_lastname'  => [
                                'field_id'    => '16',
                                'field_name'  => 'b_lastname',
                                'field_type'  => 'I',
                                'is_default'  => true,
                                'description' => 'Last name',
                                'required'    => false,
                                'value'       => null,
                            ],
                            'email'       => [
                                'field_id'    => '32',
                                'field_name'  => 'email',
                                'field_type'  => 'E',
                                'is_default'  => true,
                                'description' => 'E-mail',
                                'required'    => true,
                                'value'       => 'user@example.com',
                            ],
                            'b_phone'     => [
                                'field_id'    => '30',
                                'field_name'  => 'b_phone',
                                'field_type'  => 'I',
                                'is_default'  => true,
                                'description' => 'Phone',
                                'required'    => false,
                                'value'       => null,
                            ],
                            'b_address'   => [
                                'field_id'    => '18',
                                'field_name'  => 'b_address',
                                'field_type'  => 'I',
                                'is_default'  => true,
                                'description' => 'Address',
                                'required'    => false,
                                'value'       => null,
                            ],
                            'b_address_2' => [
                                'field_id'    => '20',
                                'field_name'  => 'b_address_2',
                                'field_type'  => 'I',
                                'is_default'  => true,
                                'description' => 'Address',
                                'required'    => false,
                                'value'       => null,
                            ],
                            'b_city'      => [
                                'field_id'    => '22',
                                'field_name'  => 'b_city',
                                'field_type'  => 'I',
                                'is_default'  => true,
                                'description' => 'City',
                                'required'    => false,
                                'value'       => null,
                            ],
                            'b_country'   => [
                                'field_id'    => '26',
                                'field_name'  => 'b_country',
                                'field_type'  => 'O',
                                'is_default'  => true,
                                'description' => 'Country',
                                'required'    => false,
                                'values'      => [
                                    'NL' => 'Netherlands',
                                    'US' => 'United States',
                                ],
                                'value'       => null,
                            ],
                            'b_state'     => [
                                'field_id'    => '24',
                                'field_name'  => 'b_state',
                                'field_type'  => 'A',
                                'is_default'  => true,
                                'description' => 'State/province',
                                'required'    => false,
                                'values'      => [
                                    'NL' => [
                                        'DR' => 'Drenthe',
                                        'FL' => 'Flevoland',
                                        'FR' => 'Friesland',
                                    ],
                                    'US' => [
                                        'AL' => 'Alabama',
                                        'AK' => 'Alaska',
                                        'AZ' => 'Arizona',
                                    ],
                                ],
                                'value'       => null,
                            ],
                            'b_zipcode'   => [
                                'field_id'    => '28',
                                'field_name'  => 'b_zipcode',
                                'field_type'  => 'Z',
                                'is_default'  => true,
                                'description' => 'Zip/postal code',
                                'required'    => false,
                                'value'       => null,
                            ],
                        ],
                    ],
                    'S' => [
                        'description' => 'Shipping address',
                        'fields'      => [
                            52            => [
                                'field_id'    => '52',
                                'field_name'  => 's_bar',
                                'field_type'  => 'I',
                                'is_default'  => false,
                                'description' => 'Bar',
                                'required'    => true,
                                'value'       => 'FOOBAR',
                            ],
                            's_firstname' => [
                                'field_id'    => '15',
                                'field_name'  => 's_firstname',
                                'field_type'  => 'I',
                                'is_default'  => true,
                                'description' => 'First name',
                                'required'    => false,
                                'value'       => null,
                            ],
                            's_lastname'  => [
                                'field_id'    => '17',
                                'field_name'  => 's_lastname',
                                'field_type'  => 'I',
                                'is_default'  => true,
                                'description' => 'Last name',
                                'required'    => false,
                                'value'       => null,
                            ],
                            's_phone'     => [
                                'field_id'    => '31',
                                'field_name'  => 's_phone',
                                'field_type'  => 'I',
                                'is_default'  => true,
                                'description' => 'Phone',
                                'required'    => false,
                                'value'       => null,
                            ],
                            's_address'   => [
                                'field_id'    => '19',
                                'field_name'  => 's_address',
                                'field_type'  => 'I',
                                'is_default'  => true,
                                'description' => 'Address',
                                'required'    => false,
                                'value'       => null,
                            ],
                            's_address_2' => [
                                'field_id'    => '21',
                                'field_name'  => 's_address_2',
                                'field_type'  => 'I',
                                'is_default'  => true,
                                'description' => 'Address',
                                'required'    => false,
                                'value'       => null,
                            ],
                            's_city'      => [
                                'field_id'    => '23',
                                'field_name'  => 's_city',
                                'field_type'  => 'I',
                                'is_default'  => true,
                                'description' => 'City',
                                'required'    => false,
                                'value'       => null,
                            ],
                            's_country'   => [
                                'field_id'    => '27',
                                'field_name'  => 's_country',
                                'field_type'  => 'O',
                                'is_default'  => true,
                                'description' => 'Country',
                                'required'    => false,
                                'values'      => [
                                    'NL' => 'Netherlands',
                                    'US' => 'United States',
                                ],
                                'value'       => null,
                            ],
                            's_state'     => [
                                'field_id'    => '25',
                                'field_name'  => 's_state',
                                'field_type'  => 'A',
                                'is_default'  => true,
                                'description' => 'State/province',
                                'required'    => false,
                                'values'      => [
                                    'NL' => [
                                        'DR' => 'Drenthe',
                                        'FL' => 'Flevoland',
                                        'FR' => 'Friesland',
                                    ],
                                    'US' => [
                                        'AL' => 'Alabama',
                                        'AK' => 'Alaska',
                                        'AZ' => 'Arizona',
                                    ],
                                ],
                                'value'       => null,
                            ],
                            's_zipcode'   => [
                                'field_id'    => '29',
                                'field_name'  => 's_zipcode',
                                'field_type'  => 'Z',
                                'is_default'  => true,
                                'description' => 'Zip/postal code',
                                'required'    => false,
                                'value'       => null,
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