<?php

namespace modules\abac\migrations;

use yii\db\Migration;

/**
 * Class m210907_062623_add_new_abac_permission_for_client_chat_add_send_quotes
 */
class m210907_062623_add_new_abac_permission_for_client_chat_add_send_quotes extends Migration
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
            'ap_object' => 'client-chat/client-chat/act/create-send-quote',
            'ap_action' => '(create)',
            'ap_action_json' => "[\"create\"]",
            'ap_effect' => 1,
            'ap_title' => 'Access to add and send quote on chat dashboard page',
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
            'client-chat/client-chat/act/create-send-quote',
        ]]);
        \Yii::$app->abac->invalidatePolicyCache();
    }
}
