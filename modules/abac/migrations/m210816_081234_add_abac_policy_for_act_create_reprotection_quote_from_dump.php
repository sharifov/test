<?php

namespace modules\abac\migrations;

use yii\db\Migration;
use Yii;

/**
 * Class m210816_081234_add_abac_policy_for_act_create_reprotection_quote_from_dump
 */
class m210816_081234_add_abac_policy_for_act_create_reprotection_quote_from_dump extends Migration
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
            'ap_object' => 'case/case/act/flight-reprotection-quote',
            'ap_action' => '(create)',
            'ap_action_json' => "[\"create\"]",
            'ap_effect' => 1,
            'ap_title' => 'Create reprotection quote from dump',
            'ap_sort_order' => 50,
            'ap_enabled' => 1,
            'ap_created_dt' => date('Y-m-d H:i:s'),
        ]);

        Yii::$app->abac->invalidatePolicyCache();
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->delete('{{%abac_policy}}', ['IN', 'ap_object', [
            'case/case/act/flight-reprotection-quote'
        ]]);
        Yii::$app->abac->invalidatePolicyCache();
    }
}
