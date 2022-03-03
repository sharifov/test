<?php

namespace modules\abac\migrations;

use yii\db\Migration;

/**
 * Class m220301_131731_add_abac_permission_for_move_lead_to_close
 */
class m220301_131731_add_abac_permission_for_move_lead_to_close extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->insert('{{%abac_policy}}', [
            'ap_rule_type' => 'p',
            'ap_subject' => '("admin" in r.sub.env.user.roles) || (r.sub.status_id != 18)',
            'ap_subject_json' => '{"condition":"OR","rules":[{"id":"env_user_roles","field":"env.user.roles","type":"string","input":"select","operator":"in_array","value":"admin"},{"id":"lead/lead/status_id","field":"status_id","type":"integer","input":"select","operator":"!=","value":18}],"valid":true}',
            'ap_object' => 'lead/lead/obj/lead',
            'ap_action' => '(close)',
            'ap_action_json' => "[\"close\"]",
            'ap_effect' => 1,
            'ap_title' => 'Access to close lead on lead view page',
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
        $this->delete('{{%abac_policy}}', [['IN', 'ap_object', [
            'lead/lead/obj/lead',
        ]], ['ap_action' => '(close)']]);
        \Yii::$app->abac->invalidatePolicyCache();
    }
}
