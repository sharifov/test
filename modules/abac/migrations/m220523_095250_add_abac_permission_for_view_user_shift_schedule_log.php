<?php

namespace modules\abac\migrations;

use modules\abac\src\entities\AbacPolicy;
use yii\db\Migration;

/**
 * Class m220523_095250_add_abac_permission_for_view_user_shift_schedule_log
 */
class m220523_095250_add_abac_permission_for_view_user_shift_schedule_log extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->insert('{{%abac_policy}}', [
            'ap_rule_type' => 'p',
            'ap_subject' => '("admin" in r.sub.env.user.roles)',
            'ap_subject_json' => '{"condition":"AND","rules":[{"id":"env_user_roles","field":"env.user.roles","type":"string","input":"select","operator":"in_array","value":"admin"}],"valid":true}',
            'ap_object' => 'shift/shift/obj/user_shift_calendar',
            'ap_action' => '(viewEventLogs)',
            'ap_action_json' => "[\"viewEventLogs\"]",
            'ap_effect' => 1,
            'ap_title' => 'Access to view event logs',
            'ap_sort_order' => 50,
            'ap_enabled' => 1,
            'ap_created_dt' => date('Y-m-d H:i:s'),
        ]);
        \Yii::$app->abac->invalidatePolicyCache();
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        if (AbacPolicy::deleteAll(['AND', ['IN', 'ap_object', ['shift/shift/obj/user_shift_calendar',]], ['IN', 'ap_action', ['(viewEventLogs)']]])) {
            \Yii::$app->abac->invalidatePolicyCache();
        }
    }
}
