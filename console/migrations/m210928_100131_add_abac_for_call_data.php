<?php

use modules\abac\src\entities\AbacPolicy;
use yii\db\Migration;

/**
 * Class m210928_100131_add_abac_for_call_data
 */
class m210928_100131_add_abac_for_call_data extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        if ($allowList = AbacPolicy::findOne(['ap_object' => 'call/call/act/allow-list'])) {
            $allowList->ap_object = 'call/call/act/data-allow-list';
            $allowList->ap_action = '(toggle_data)';
            $allowList->ap_action_json = "[\"toggle_data\"]";
            $allowList->save();
        }

        if (!AbacPolicy::find()->where(['ap_object' => 'call/call/act/data-is-trusted'])->exists()) {
            $this->insert('{{%abac_policy}}', [
                'ap_rule_type' => 'p',
                'ap_subject' => '("admin" in r.sub.env.user.roles)',
                'ap_subject_json' => '{"condition":"AND","rules":[{"id":"env_user_roles","field":"env.user.roles","type":"string","input":"select","operator":"in_array","value":"admin"}],"valid":true}',
                'ap_object' => 'call/call/act/data-is-trusted',
                'ap_action' => '(toggle_data)',
                'ap_action_json' => "[\"toggle_data\"]",
                'ap_effect' => 1,
                'ap_title' => 'Access to add/remove ContactPhoneData - key "is_trusted"',
                'ap_sort_order' => 50,
                'ap_enabled' => 1,
                'ap_created_dt' => date('Y-m-d H:i:s'),
            ]);
        }
        if (!AbacPolicy::find()->where(['ap_object' => 'call/call/act/data-auto-create-case-off'])->exists()) {
            $this->insert('{{%abac_policy}}', [
                'ap_rule_type' => 'p',
                'ap_subject' => '("admin" in r.sub.env.user.roles)',
                'ap_subject_json' => '{"condition":"AND","rules":[{"id":"env_user_roles","field":"env.user.roles","type":"string","input":"select","operator":"in_array","value":"admin"}],"valid":true}',
                'ap_object' => 'call/call/act/data-auto-create-case-off',
                'ap_action' => '(toggle_data)',
                'ap_action_json' => "[\"toggle_data\"]",
                'ap_effect' => 1,
                'ap_title' => 'Access to add/remove ContactPhoneData - key "auto_create_case_off"',
                'ap_sort_order' => 50,
                'ap_enabled' => 1,
                'ap_created_dt' => date('Y-m-d H:i:s'),
            ]);
        }
        if (!AbacPolicy::find()->where(['ap_object' => 'call/call/act/data-auto-create-lead-off'])->exists()) {
            $this->insert('{{%abac_policy}}', [
                'ap_rule_type' => 'p',
                'ap_subject' => '("admin" in r.sub.env.user.roles)',
                'ap_subject_json' => '{"condition":"AND","rules":[{"id":"env_user_roles","field":"env.user.roles","type":"string","input":"select","operator":"in_array","value":"admin"}],"valid":true}',
                'ap_object' => 'call/call/act/data-auto-create-lead-off',
                'ap_action' => '(toggle_data)',
                'ap_action_json' => "[\"toggle_data\"]",
                'ap_effect' => 1,
                'ap_title' => 'Access to add/remove ContactPhoneData - key "auto_create_lead_off"',
                'ap_sort_order' => 50,
                'ap_enabled' => 1,
                'ap_created_dt' => date('Y-m-d H:i:s'),
            ]);
        }

        \Yii::$app->abac->invalidatePolicyCache();
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        if ($allowList = AbacPolicy::findOne(['ap_object' => 'call/call/act/data-allow-list'])) {
            $allowList->ap_object = 'call/call/act/allow-list';
            $allowList->ap_action = '(update)';
            $allowList->ap_action_json = "[\"update\"]";
            $allowList->save();
        }

        AbacPolicy::deleteAll(['ap_object' => 'call/call/act/data-is-trusted']);
        AbacPolicy::deleteAll(['ap_object' => 'call/call/act/data-auto-create-case-off']);
        AbacPolicy::deleteAll(['ap_object' => 'call/call/act/data-auto-create-lead-off']);

        \Yii::$app->abac->invalidatePolicyCache();
    }
}
