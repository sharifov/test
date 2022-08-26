<?php

use common\models\Department;
use src\model\department\department\Type;
use yii\db\Migration;

/**
 * Class m220824_085014_add_new_department_cross_sell
 */
class m220824_085014_add_new_department_cross_sell extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $dep_params = [
            'default_phone_type' => 'Only personal',
            'object' => [
                'type' => Type::CASE,
                'lead' => [
                    'createOnCall' => [
                        'createOnGeneralLineCall' => false,
                        'createOnDirectCall' => false,
                        'createOnRedirectCall' => false,
                    ],
                    'createOnSms' => false,
                    'createOnDepartmentEmail' => [],
                    'createOnPersonalEmail' => [],
                    'callDefaultPhoneType' => 'personal',
                    'smsDefaultPhoneType' => 'personal',
                    'emailDefaultType' => 'personal',
                ],
                'case' => [
                    'createOnCall' => [
                        'createOnGeneralLineCall' => true,
                        'createOnDirectCall' => true,
                        'createOnRedirectCall' => true,
                    ],
                    'createOnSms' => false,
                    'trashActiveDaysLimit' => 14,
                    'sendFeedback' => false,
                    'feedbackTemplateTypeKey' => '',
                    'feedbackEmailFrom' => '',
                    'feedbackNameFrom' => '',
                    'feedbackBookingIdRequired' => false,
                    'createOnDepartmentEmail' => [
                    ],
                    'createOnPersonalEmail' => [
                    ],
                    'callDefaultPhoneType' => 'personal',
                    'smsDefaultPhoneType' => 'personal',
                    'emailDefaultType' => 'personal',
                ],
            ],
            'call_recording_disabled' => false,
            'queue_distribution' => [
                'time_start_call_user_access_general' => null,
                'general_line_user_limit' => null,
                'time_repeat_call_user_access' => null,
                'call_distribution_sort' => [
                    'general_line_call_count' => 'ASC',
                    'phone_ready_time' => 'ASC',
                    'priority_level' => 'DESC',
                    'gross_profit' => 'DESC',
                ],
            ],
            'warm_transfer' => [
                'timeout' => null,
                'auto_unhold_enabled' => null,
            ],
        ];
        $this->insert('{{%department}}', [
            'dep_id'              => Department::DEPARTMENT_CROSS_SELL,
            'dep_key'             => 'cross_sell',
            'dep_name'            => 'Cross Sell',
            'dep_updated_user_id' => null,
            'dep_updated_dt'      => date('Y-m-d H:i:s'),
            'dep_params'          => json_encode($dep_params),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->delete('{{%department}}', [
            'dep_id' => Department::DEPARTMENT_CROSS_SELL
        ]);
    }
}
