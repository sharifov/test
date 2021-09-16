<?php

namespace modules\abac\migrations;

use yii\db\Migration;

/**
 * Class m210915_140607_add_new_abac_permission_for_creation_leads_from_phone_widget_ui
 */
class m210915_140607_add_new_abac_permission_for_creation_leads_from_phone_widget_ui extends Migration
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
            'ap_object' => 'lead/lead/act/create-from-phone-widget',
            'ap_action' => '(create)',
            'ap_action_json' => "[\"create\"]",
            'ap_effect' => 1,
            'ap_title' => 'Access to create lead btn in phone widget in contact info block',
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
            'lead/lead/act/create-from-phone-widget',
        ]]);
        \Yii::$app->abac->invalidatePolicyCache();
    }
}
