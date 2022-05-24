<?php

namespace modules\abac\migrations;

use modules\abac\src\entities\AbacPolicy;
use yii\db\Migration;

/**
 * Class m220524_122147_add_abac_permission_for_edit_event_on_calendar_page
 */
class m220524_122147_add_abac_permission_for_edit_event_on_calendar_page extends Migration
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
            'ap_object' => 'shift/shift/obj/user_shift_event',
            'ap_action' => '(update)',
            'ap_action_json' => "[\"update\"]",
            'ap_effect' => 1,
            'ap_title' => 'Access to edit event in calendar page',
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
        if (AbacPolicy::deleteAll(['AND', ['IN', 'ap_object', ['shift/shift/obj/user_shift_event',]], ['IN', 'ap_action', ['(update)']]])) {
            \Yii::$app->abac->invalidatePolicyCache();
        }
    }
}
