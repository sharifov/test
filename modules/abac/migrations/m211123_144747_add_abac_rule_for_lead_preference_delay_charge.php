<?php

namespace modules\abac\migrations;

use yii\db\Migration;

/**
 * Class m211123_144747_add_abac_rule_for_lead_preference_delay_charge
 */
class m211123_144747_add_abac_rule_for_lead_preference_delay_charge extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->insert('{{%abac_policy}}', [
            'ap_rule_type' => 'p',
            'ap_subject' => '(r.sub.env.available == true)',
            'ap_subject_json' => '{"condition":"AND","rules":[{"id":"env_available","field":"env.available","type":"boolean","input":"radio","operator":"==","value":true}],"valid":true}',
            'ap_object' => 'lead/lead/obj/lead_preferences',
            'ap_action' => '(setDelayedCharge)',
            'ap_action_json' => "[\"setDelayedCharge\"]",
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
        $this->delete('{{%abac_policy}}', ['AND', ['IN', 'ap_object', [
            'lead/lead/obj/lead_preferences',
        ]], ['IN', 'ap_action', ['(setDelayedCharge)']]]);
    }
}
