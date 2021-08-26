<?php

namespace modules\abac\migrations;

use yii\db\Migration;

/**
 * Class m210826_090948_add_new_abac_permission_for_set_recommended_reprotection_quote
 */
class m210826_090948_add_new_abac_permission_for_set_recommended_reprotection_quote extends Migration
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
            'ap_object' => 'case/case/act/reprotection_quote/set_recommended',
            'ap_action' => '(access)',
            'ap_action_json' => "[\"access\"]",
            'ap_effect' => 1,
            'ap_title' => 'Access to set recommended reprotection quote',
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
            'case/case/act/reprotection_quote/set_recommended',
        ]]);
        \Yii::$app->abac->invalidatePolicyCache();
    }
}
