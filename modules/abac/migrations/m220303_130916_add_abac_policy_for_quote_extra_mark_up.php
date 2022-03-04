<?php

namespace modules\abac\migrations;

use yii\db\Migration;

/**
 * Class m220303_130916_add_abac_policy_for_quote_extra_mark_up
 */
class m220303_130916_add_abac_policy_for_quote_extra_mark_up extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {

        $this->insert('{{%abac_policy}}', [
            'ap_rule_type' => 'p',
            'ap_subject' => '(r.sub.quote_status_id in [1]) && (r.sub.is_owner == true) && (r.sub.lead_status_id in [2])',
            'ap_subject_json' => '{"condition":"AND","rules":[{"id":"lead-view/quote_status__id","field":"quote_status_id","type":"integer","input":"select","operator":"==","value":[1]},{"id":"lead-view/is_owner","field":"is_owner","type":"boolean","input":"select","operator":"==","value":true},{"id":"lead-view/lead_status_id","field":"lead_status_id","type":"integer","input":"select","operator":"==","value":[2]}],"valid":true}',
            'ap_object' => 'lead-view/action-ajax-edit-lead-quote-extra-mark-up-modal-content',
            'ap_action' => '(edit)',
            'ap_action_json' => "[\"view\",\"edit\"]",
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
                'lead-view/action-ajax-edit-lead-quote-extra-mark-up-modal-content',
            ]
        ]);
        \Yii::$app->abac->invalidatePolicyCache();
    }
}
