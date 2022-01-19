<?php

namespace modules\abac\migrations;

use yii\db\Migration;

/**
 * Class m220119_123226_add_abac_permissions_for_user_feedback_tool
 */
class m220119_123226_add_abac_permissions_for_user_feedback_tool extends Migration
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
            'ap_object' => 'user/user/obj/user-feedback',
            'ap_action' => '(multipleUpdate)|(create)',
            'ap_action_json' => "[\"multipleUpdate\",\"create\"]",
            'ap_effect' => 1,
            'ap_title' => 'Access to user feedback tool',
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
        $this->delete('{{%abac_policy}}', ['IN', 'ap_object', [
            'user/user/obj/user-feedback',
        ]]);
        \Yii::$app->abac->invalidatePolicyCache();
    }
}
