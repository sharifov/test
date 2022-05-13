<?php

namespace modules\abac\migrations;

use yii\db\Migration;

/**
 * Class m220511_143435_add_abac_policy_for_phone_number_redial_multiple_delete_btn
 */
class m220511_143435_add_abac_policy_for_phone_number_redial_multiple_delete_btn extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        try {
            $this->insert('{{%abac_policy}}', [
                'ap_rule_type'    => 'p',
                'ap_subject'      => '("admin" in r.sub.env.user.roles)',
                'ap_subject_json' => '{"condition":"AND","rules":[{"id":"env_user_roles","field":"env.user.roles","type":"string","input":"select","operator":"in_array","value":"admin"}],"valid":true}',
                'ap_object'       => 'phone-number-redial/phone-number-redial/obj/phone-number-redial',
                'ap_action'       => '(multiple-delete)',
                'ap_action_json'  => json_encode(['multiple-delete']),
                'ap_effect'       => 1,
                'ap_title'        => 'Phone number redial access multiple delete',
                'ap_sort_order'   => 50,
                'ap_enabled'      => 1,
                'ap_created_dt'   => date('Y-m-d H:i:s'),
            ]);
            \Yii::$app->abac->invalidatePolicyCache();
        } catch (\Throwable $throwable) {
            \Yii::error(
                $throwable,
                'm220511_143435_add_abac_policy_for_phone_number_redial_multiple_delete_btn:safeUp:Throwable'
            );
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        try {
            $this->delete('{{%abac_policy}}', [
                'IN',
                'ap_object',
                [
                    'phoneNumberRedial/phoneNumberRedial/obj/phone-number-redial',
                ]
            ]);
            \Yii::$app->abac->invalidatePolicyCache();
        } catch (\Throwable $throwable) {
            \Yii::error(
                $throwable,
                'm220511_143435_add_abac_policy_for_phone_number_redial_multiple_delete_btn:safeDown:Throwable'
            );
        }
    }
}
