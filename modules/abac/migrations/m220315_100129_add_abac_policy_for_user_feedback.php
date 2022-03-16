<?php

namespace modules\abac\migrations;

use yii\db\Migration;

/**
 * Class m220315_100129_add_abac_policy_for_user_feedback
 */
class m220315_100129_add_abac_policy_for_user_feedback extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {

        $this->insert('{{%abac_policy}}', [
            'ap_rule_type' => 'p',
            'ap_subject' => '(r.sub.is_owner == true)',
            'ap_subject_json' => '{"condition":"AND","rules":[{"id":"user-feedback/is_owner","field":"is_owner","type":"boolean","input":"select","operator":"==","value":true}],"valid":true}',
            'ap_object' => 'user-feedback/obj/user-feedback',
            'ap_action' => '(read)',
            'ap_action_json' => "[\"read\"]",
            'ap_effect' => 1,
            'ap_title' => '',
            'ap_sort_order' => 50,
            'ap_enabled' => 1,
            'ap_created_dt' => date('Y-m-d H:i:s'),
        ]);
        $this->insert('{{%abac_policy}}', [
            'ap_rule_type' => 'p',
            'ap_subject' => '(r.sub.env.available == true)',
            'ap_subject_json' => '{"condition":"OR","rules":[{"id":"env_available","field":"env.available","type":"boolean","input":"radio","operator":"==","value":true}],"valid":true}',
            'ap_object' => 'user-feedback/obj/user-feedback',
            'ap_action' => '(create)',
            'ap_action_json' => "[\"create\"]",
            'ap_effect' => 1,
            'ap_title' => '',
            'ap_sort_order' => 50,
            'ap_enabled' => 1,
            'ap_created_dt' => date('Y-m-d H:i:s'),
        ]);
        $this->insert('{{%abac_policy}}', [
            'ap_rule_type' => 'p',
            'ap_subject' => '(r.sub.env.available == true)',
            'ap_subject_json' => '{"condition":"OR","rules":[{"id":"env_available","field":"env.available","type":"boolean","input":"radio","operator":"==","value":true}],"valid":true}',
            'ap_object' => 'user-feedback/act/user-feedback-index',
            'ap_action' => '(access)',
            'ap_action_json' => "[\"access\"]",
            'ap_effect' => 1,
            'ap_title' => '',
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
        $this->delete('{{%abac_policy}}', [
            'IN',
            'ap_object',
            [
                'user-feedback/act/user-feedback-index',
                'user-feedback/obj/user-feedback',
            ]
        ]);
        \Yii::$app->abac->invalidatePolicyCache();
    }
}
