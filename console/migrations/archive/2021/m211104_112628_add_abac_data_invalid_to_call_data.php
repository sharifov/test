<?php

use modules\abac\src\entities\AbacPolicy;
use yii\db\Migration;

/**
 * Class m211104_112628_add_abac_data_invalid_to_call_data
 */
class m211104_112628_add_abac_data_invalid_to_call_data extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        if (!AbacPolicy::find()->where(['ap_object' => 'call/call/act/data-invalid'])->exists()) {
            $this->insert('{{%abac_policy}}', [
                'ap_rule_type' => 'p',
                'ap_subject' => '("admin" in r.sub.env.user.roles)',
                'ap_subject_json' => '{"condition":"AND","rules":[{"id":"env_user_roles","field":"env.user.roles","type":"string","input":"select","operator":"in_array","value":"admin"}],"valid":true}',
                'ap_object' => 'call/call/act/data-invalid',
                'ap_action' => '(toggle_data)',
                'ap_action_json' => "[\"toggle_data\"]",
                'ap_effect' => 1,
                'ap_title' => 'Access to add/remove ContactPhoneData - key "invalid"',
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
        AbacPolicy::deleteAll(['ap_object' => 'call/call/act/data-invalid']);

        \Yii::$app->abac->invalidatePolicyCache();
    }
}
