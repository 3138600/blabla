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

namespace Tygh\Api\Entities\v40;

use Tygh\Addons\StorefrontRestApi\ProfileFields\Manager as ProfileFieldsManager;
use Tygh\Api\Entities\Users;
use Tygh\Api\Response;
use Tygh\Enum\ProfileFieldAreas;
use Tygh\Tygh;

/**
 * Class SraProfile provides means to read and update current user profile or create the new one.
 *
 * @package Tygh\Api\Entities\v40
 */
class SraProfile extends Users
{
    /** @var \Tygh\Addons\StorefrontRestApi\ProfileFields\Manager $manager */
    protected $manager;

    /** @inheritdoc */
    public function __construct(array $auth = [], $area = '')
    {
        parent::__construct($auth, $area);

        /** @var \Tygh\Addons\StorefrontRestApi\ProfileFields\Manager manager */
        $this->manager = Tygh::$app['addons.storefront_rest_api.profile_fields.manager'];

        $this->manager->setFieldFilter('fn_storefront_rest_api_filter_profile_fields');
    }

    /** @inheritdoc */
    public function index($id = 0, $params = [])
    {
        $params = array_merge([
            'lang_code' => DEFAULT_LANGUAGE,
        ], $params);

        $id = $this->auth['user_id'];

        $result = parent::index($id, $params);

        if ($result['status'] === Response::STATUS_OK) {
            $user_info = fn_get_user_info($id, true);

            $schema = $this->manager->get(
                ProfileFieldAreas::PROFILE,
                ProfileFieldsManager::ACTION_UPDATE,
                $this->auth,
                $params['lang_code']
            );

            $result['data']['fields'] = $this->manager->hydrate($schema, $user_info);
        }

        return $result;
    }

    /** @inheritdoc */
    public function create($params)
    {
        $params = fn_array_merge([
            'lang_code' => DEFAULT_LANGUAGE,
        ], $params);

        $params['company_id'] = fn_get_runtime_company_id();
        $params['user_type'] = 'C';

        $schema = $this->manager->get(
            ProfileFieldAreas::PROFILE,
            ProfileFieldsManager::ACTION_ADD,
            $this->auth,
            $params['lang_code']
        );

        $params = $this->manager->split($schema, $params);
        $fields_validation = $this->manager->validate($schema, $params);

        if (!$fields_validation->isSuccess()) {
            $errors = [];
            foreach ($fields_validation->getData() as $err_type => $schema) {
                foreach ($schema as $field) {
                    if ($err_type === 'required') {
                        $errors[] = $this->getRequiredFieldErrorMessage($field);
                    } else {
                        $errors[] = $this->getInvalidFieldErrorMessage($field);
                    }
                }
            }

            return [
                'status' => Response::STATUS_BAD_REQUEST,
                'data'   => [
                    'message' => implode('; ', $errors),
                ],
            ];
        }

        $result = parent::create($params);

        if ($result['status'] === Response::STATUS_CREATED) {
            list($token, $expiry_time) = fn_get_user_auth_token($result['data']['user_id']);
            $result['data']['auth'] = [
                'token' => $token,
                'ttl'   => $expiry_time - TIME,
            ];
        }

        return $result;
    }

    /** @inheritdoc */
    public function update($id, $params)
    {
        $id = $this->auth['user_id'];

        $params = fn_array_merge([
            'lang_code' => DEFAULT_LANGUAGE,
        ], $params);

        $params['company_id'] = fn_get_runtime_company_id();
        $params['user_type'] = 'C';

        $schema = $this->manager->get(
            ProfileFieldAreas::PROFILE,
            ProfileFieldsManager::ACTION_UPDATE,
            $this->auth,
            $params['lang_code']
        );

        $params = $this->manager->split($schema, $params);
        $fields_validation = $this->manager->validate($schema, $params);

        if (!$fields_validation->isSuccess()) {
            $errors = [];
            foreach ($fields_validation->getData() as $err_type => $schema) {
                foreach ($schema as $field) {
                    if ($err_type === 'required') {
                        $errors[] = $this->getRequiredFieldErrorMessage($field);
                    } else {
                        $errors[] = $this->getInvalidFieldErrorMessage($field);
                    }
                }
            }

            return [
                'status' => Response::STATUS_BAD_REQUEST,
                'data'   => [
                    'message' => implode('; ', $errors),
                ],
            ];
        }

        return parent::update($id, $params);
    }

    /** @inheritdoc */
    public function delete($id)
    {
        return [
            'status' => Response::STATUS_METHOD_NOT_ALLOWED,
        ];
    }

    /** @inheritdoc */
    public function privilegesCustomer()
    {
        return [
            'create' => true,
            'update' => $this->auth['is_token_auth'],
            'delete' => false,
            'index'  => $this->auth['is_token_auth'],
        ];
    }

    /**
     * Gets request field name.
     *
     * @param array $field Request field
     *
     * @return string
     */
    protected function getFieldName(array $field)
    {
        $field_name = $field['field_id'];

        if (!$field['is_default']) {
            $field_name = sprintf('fields[%s]', $field_name);
        }

        return $field_name;
    }

    /**
     * Gets required field error message.
     *
     * @param array $field Required field
     *
     * @return string Message
     */
    protected function getRequiredFieldErrorMessage(array $field)
    {
        $field_name = $this->getFieldName($field);

        return __('api_required_field', [
                '[field]' => $field_name,
            ]
        );
    }

    /**
     * Gets invalid field error message.
     *
     * @param array $field Invalid field
     *
     * @return string Message
     */
    protected function getInvalidFieldErrorMessage(array $field)
    {
        $field_name = $this->getFieldName($field);

        return __('api_invalid_value_w_valid_list', [
            '[field]'      => $field_name,
            '[value]'      => $field['value'],
            '[valid_list]' => implode(', ', array_keys($field['values'])),
        ]);
    }
}