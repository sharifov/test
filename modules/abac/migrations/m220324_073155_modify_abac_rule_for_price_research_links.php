<?php

namespace modules\abac\migrations;

use yii\db\Migration;

/**
 * Class m220324_073155_modify_abac_rule_for_price_research_links
 */
class m220324_073155_modify_abac_rule_for_price_research_links extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->delete('{{%abac_policy}}', [
            'IN',
            'ap_object',
            [
                'lead/lead/act/price-link-research',
            ]
        ]);

        $this->insert('{{%abac_policy}}', [
            'ap_rule_type' => 'p',
            'ap_subject' => '(r.sub.is_owner == true) && (r.sub.status_id == 2) && (r.sub.flightSegmentsCount > 0)',
            'ap_subject_json' => '{"condition":"AND","rules":[{"id":"lead/lead/is_owner","field":"is_owner","type":"boolean","input":"radio","operator":"==","value":true},{"id":"lead/lead/status_id","field":"status_id","type":"integer","input":"select","operator":"==","value":2},{"id":"lead/lead/flightSegmentsCount","field":"flightSegmentsCount","type":"integer","input":"text","operator":">","value":0}],"valid":true}',
            'ap_object' => 'lead/lead/act/price-link-research',
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
                'lead/lead/act/price-link-research',
            ]
        ]);
        $this->insert('{{%abac_policy}}', [
            'ap_rule_type' => 'p',
            'ap_subject' => '(r.sub.is_owner == true) && (r.sub.status_id == 2)',
            'ap_subject_json' => '{"condition":"AND","rules":[{"id":"lead/lead/is_owner","field":"is_owner","type":"boolean","input":"radio","operator":"==","value":true},{"id":"lead/lead/status_id","field":"status_id","type":"integer","input":"select","operator":"==","value":2}],"valid":true}',
            'ap_object' => 'lead/lead/act/price-link-research',
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
}
