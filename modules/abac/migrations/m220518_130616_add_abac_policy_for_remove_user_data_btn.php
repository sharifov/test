<?php

namespace modules\abac\migrations;

use yii\db\Migration;

/**
 * Class m220518_130616_add_abac_policy_for_remove_user_data_btn
 */
class m220518_130616_add_abac_policy_for_remove_user_data_btn extends Migration
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
                'ap_object'       => 'shift/shift/act/my_shift_schedule',
                'ap_action'       => '(removeAllUserSchedule)',
                'ap_action_json'  => json_encode(['removeAllUserSchedule']),
                'ap_effect'       => 1,
                'ap_title'        => 'My shift schedule access removeAllUserSchedule',
                'ap_sort_order'   => 50,
                'ap_enabled'      => 1,
                'ap_created_dt'   => date('Y-m-d H:i:s'),
            ]);
            \Yii::$app->abac->invalidatePolicyCache();
        } catch (\Throwable $throwable) {
            \Yii::error(
                $throwable,
                'm220518_130616_add_abac_policy_for_remove_user_data_btn:safeUp:Throwable'
            );
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        try {
            $this->delete('{{%abac_policy}}', ['AND', ['IN', 'ap_object', ['shift/shift/act/my_shift_schedule']], ['IN', 'ap_action', ['(removeAllUserSchedule)']]
            ]);
            \Yii::$app->abac->invalidatePolicyCache();
        } catch (\Throwable $throwable) {
            \Yii::error(
                $throwable,
                'm220518_130616_add_abac_policy_for_remove_user_data_btn:safeDown:Throwable'
            );
        }
    }
}
