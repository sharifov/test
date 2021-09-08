<?php

namespace modules\abac\migrations;

use yii\db\Migration;

/**
 * Class m210810_095607_add_new_abac_permission
 */
class m210810_095607_add_new_abac_permission extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->insert('{{%abac_policy}}', [
            'ap_rule_type' => 'p',
            'ap_subject' => '(is_owner == true) || ("admin" in env.user.roles) || ("superadmin" in env.user.roles)',
            'ap_subject_json' => '{"condition":"OR","rules":[{"id":"case/case/is_owner","field":"is_owner","type":"boolean","input":"radio","operator":"==","value":true},{"id":"env_user_roles","field":"env.user.roles","type":"string","input":"select","operator":"in_array","value":"admin"},{"id":"env_user_roles","field":"env.user.roles","type":"string","input":"select","operator":"in_array","value":"superadmin"}],"valid":true}',
            'ap_object' => 'case/case/reprotection_quote/send_email',
            'ap_action' => '(access)',
            'ap_action_json' => "[\"access\"]",
            'ap_effect' => 1,
            'ap_title' => 'Reprotection Quote Send Email',
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
            'case/case/reprotection_quote/send_email',
        ]]);
        \Yii::$app->abac->invalidatePolicyCache();
    }
}
