<?php

namespace modules\abac\migrations;

use modules\abac\src\entities\AbacPolicy;
use yii\db\Migration;

/**
 * Class m220527_113055_add_new_abac_permission_for_permanently_delete
 */
class m220527_113055_add_new_abac_permission_for_permanently_delete extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        if (!AbacPolicy::find()->where(['ap_object' => 'shift/shift/obj/user_shift_event', 'ap_action' => '(permanentlyDelete)'])->exists()) {
            $this->insert('{{%abac_policy}}', [
                'ap_rule_type' => 'p',
                'ap_subject' => '("admin" in r.sub.env.user.roles)',
                'ap_subject_json' => '{"condition":"AND","rules":[{"id":"env_user_roles","field":"env.user.roles","type":"string","input":"select","operator":"in_array","value":"admin"}],"valid":true}',
                'ap_object' => 'shift/shift/obj/user_shift_event',
                'ap_action' => '(permanentlyDelete)',
                'ap_action_json' => "[\"permanentlyDelete\"]",
                'ap_effect' => 1,
                'ap_title' => 'Access to permanently delete event in calendar widget',
                'ap_sort_order' => 50,
                'ap_enabled' => 1,
                'ap_created_dt' => date('Y-m-d H:i:s'),
            ]);
            \Yii::$app->abac->invalidatePolicyCache();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        if (AbacPolicy::deleteAll(['AND', ['IN', 'ap_object', ['shift/shift/obj/user_shift_event',]], ['IN', 'ap_action', ['(permanentlyDelete)']]])) {
            \Yii::$app->abac->invalidatePolicyCache();
        }
    }
}
