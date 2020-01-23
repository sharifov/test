<?php

namespace webapi\models;

/**
 * This is the model class for table "api_user_allowance".
 *
 * @property int $aua_user_id
 * @property int $aua_allowed_number_requests
 * @property int $aua_last_check_time
 */
class ApiUserAllowance extends \yii\db\ActiveRecord
{
    public static function tableName(): string
    {
        return 'api_user_allowance';
    }

    public function rules(): array
    {
        return [
            [['aua_allowed_number_requests', 'aua_last_check_time'], 'required'],
            [['aua_allowed_number_requests', 'aua_last_check_time'], 'default', 'value' => null],
            [['aua_allowed_number_requests', 'aua_last_check_time'], 'integer'],
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'aua_user_id' => 'User ID',
            'aua_allowed_number_requests' => 'Allowed Number Requests',
            'aua_last_check_time' => 'Last Check Time',
        ];
    }
}
