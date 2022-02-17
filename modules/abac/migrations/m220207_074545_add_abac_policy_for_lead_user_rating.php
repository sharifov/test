<?php

namespace modules\abac\migrations;

use yii\db\Migration;

/**
 * Class M220207074545AddAbacPolicyForLeadUserRating
 */
class m220207_074545_add_abac_policy_for_lead_user_rating extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {

        $this->insert('{{%abac_policy}}', [
            'ap_rule_type' => 'p',
            'ap_subject' => '(r.sub.status_id == 2) && (r.sub.is_owner == true)',
            'ap_subject_json' => '{"condition":"AND","rules":[{"id":"lead/status_id","field":"status_id","type":"integer","input":"select","operator":"==","value":2},{"id":"lead/is_owner","field":"is_owner","type":"boolean","input":"select","operator":"==","value":true}],"valid":true}',
            'ap_object' => 'lead/set-user-rating',
            'ap_action' => '(view)|(edit)',
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
                'lead/set-user-rating',
            ]
        ]);
        \Yii::$app->abac->invalidatePolicyCache();
    }
}
