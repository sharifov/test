<?php

namespace modules\abac\migrations;

use yii\db\Migration;

/**
 * Class m220329_203144_add_abac_policy_for_search_btns
 */
class m220329_203144_add_abac_policy_for_search_btns extends Migration
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
            'ap_object' => 'lead/lead/obj/quote_search',
            'ap_action' => '(accessQuoteSearch)',
            'ap_action_json' => "[\"accessQuoteSearch\"]",
            'ap_effect' => 1,
            'ap_title' => '',
            'ap_sort_order' => 50,
            'ap_enabled' => 1,
            'ap_created_dt' => date('Y-m-d H:i:s'),
        ]);

        $this->insert('{{%abac_policy}}', [
            'ap_rule_type' => 'p',
            'ap_subject' => '(r.sub.env.available == true)',
            'ap_subject_json' => '{"condition":"AND","rules":[{"id":"env_available","field":"env.available","type":"boolean","input":"radio","operator":"==","value":true}],"valid":true}',
            'ap_object' => 'lead/lead/obj/quick_search',
            'ap_action' => '(accessQuickSearch)',
            'ap_action_json' => "[\"accessQuickSearch\"]",
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
            'lead/lead/obj/quote_search',
        ]], ['IN', 'ap_action', ['(accessQuoteSearch)']]]);

        $this->delete('{{%abac_policy}}', ['AND', ['IN', 'ap_object', [
            'lead/lead/obj/quick_search',
        ]], ['IN', 'ap_action', ['(accessQuickSearch)']]]);

        \Yii::$app->abac->invalidatePolicyCache();
    }
}
