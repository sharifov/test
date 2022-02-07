<?php

namespace modules\abac\migrations;

use yii\db\Migration;

/**
 * Class m220203_153431_add_abac_permission_for_superadmin
 */
class m220203_153431_add_abac_permission_for_superadmin extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->insert('{{%abac_policy}}', [
            'ap_rule_type' => 'p',
            'ap_subject' => '(r.sub.env.user.username == "superadmin")',
            'ap_subject_json' => '"{\"condition\":\"AND\",\"rules\":[{\"id\":\"env_username\",\"field\":\"env.user.username\",\"type\":\"string\",\"input\":\"text\",\"operator\":\"==\",\"value\":\"superadmin\"}],\"valid\":true}"',
            'ap_object' => '*',
            'ap_action' => '(access)',
            'ap_action_json' => "[\"access\"]",
            'ap_effect' => 1,
            'ap_title' => 'Access to all objects in system',
            'ap_sort_order' => 1,
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
            '*'
        ]]);
        \Yii::$app->abac->invalidatePolicyCache();
    }
}
