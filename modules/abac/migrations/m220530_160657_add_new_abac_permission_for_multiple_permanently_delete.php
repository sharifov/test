<?php

namespace modules\abac\migrations;

use modules\abac\src\entities\AbacPolicy;
use yii\db\Migration;

/**
 * Class m220530_160657_add_new_abac_permission_for_multiple_permanently_delete
 */
class m220530_160657_add_new_abac_permission_for_multiple_permanently_delete extends Migration
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
            'ap_action' => '(multiplePermanentlyDeleteEvents)',
            'ap_action_json' => "[\"multiplePermanentlyDeleteEvents\"]",
            'ap_effect' => 1,
            'ap_title' => 'Access to delete multiple permanently events',
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
        if (AbacPolicy::deleteAll(['AND', ['IN', 'ap_object', ['shift/shift/obj/user_shift_calendar',]], ['IN', 'ap_action', ['(multiplePermanentlyDeleteEvents)']]])) {
            \Yii::$app->abac->invalidatePolicyCache();
        }
    }
}
