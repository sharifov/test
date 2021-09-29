<?php

use modules\abac\src\entities\AbacPolicy;
use yii\db\Migration;

/**
 * Class m210923_135256_add_abac_for_call
 */
class m210923_135256_add_abac_for_call extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        if (!AbacPolicy::find()->where(['ap_object' => 'call/call/act/allow-list'])->exists()) {
            $this->insert('{{%abac_policy}}', [
                'ap_rule_type' => 'p',
                'ap_subject' => '("admin" in r.sub.env.user.roles)',
                'ap_subject_json' => '{"condition":"AND","rules":[{"id":"env_user_roles","field":"env.user.roles","type":"string","input":"select","operator":"in_array","value":"admin"}],"valid":true}',
                'ap_object' => 'call/call/act/allow-list',
                'ap_action' => '(update)',
                'ap_action_json' => "[\"update\"]",
                'ap_effect' => 1,
                'ap_title' => 'Access to add/remove ContactPhoneData - key "allow_list"',
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
        if (AbacPolicy::deleteAll(['ap_object' => 'call/call/act/allow-list'])) {
            \Yii::$app->abac->invalidatePolicyCache();
        }
    }
}
