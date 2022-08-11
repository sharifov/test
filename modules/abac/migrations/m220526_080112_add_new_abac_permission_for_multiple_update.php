<?php

namespace modules\abac\migrations;

use modules\abac\src\entities\AbacPolicy;
use yii\db\Migration;

/**
 * Class m220526_080112_add_new_abac_permission_for_multiple_update
 */
class m220526_080112_add_new_abac_permission_for_multiple_update extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        if (!AbacPolicy::find()->where(['ap_object' => 'shift/shift/obj/user_shift_calendar', 'ap_action' => '(multipleUpdateEvents)'])->exists()) {
            $this->insert('{{%abac_policy}}', [
                'ap_rule_type' => 'p',
                'ap_subject' => '("admin" in r.sub.env.user.roles)',
                'ap_subject_json' => '{"condition":"AND","rules":[{"id":"env_user_roles","field":"env.user.roles","type":"string","input":"select","operator":"in_array","value":"admin"}],"valid":true}',
                'ap_object' => 'shift/shift/obj/user_shift_calendar',
                'ap_action' => '(multipleUpdateEvents)',
                'ap_action_json' => "[\"multipleUpdateEvents\"]",
                'ap_effect' => 1,
                'ap_title' => 'Access to update multiple events',
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
        if (AbacPolicy::deleteAll(['AND', ['IN', 'ap_object', ['shift/shift/obj/user_shift_calendar',]], ['IN', 'ap_action', ['(multipleUpdateEvents)']]])) {
            \Yii::$app->abac->invalidatePolicyCache();
        }
    }
}
