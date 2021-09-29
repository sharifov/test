<?php

namespace modules\abac\migrations;

use yii\db\Migration;

/**
 * Class m210920_112148_add_abac_permission_for_take_lead_from_call
 */
class m210920_112148_add_abac_permission_for_take_lead_from_call extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->insert('{{%abac_policy}}', [
            'ap_rule_type' => 'p',
            'ap_subject' => '(r.sub.status_id == 1)',
            'ap_subject_json' => '{"condition":"AND","rules":[{"id":"lead/lead/status_id","field":"status_id","type":"integer","input":"select","operator":"==","value":1}],"valid":true}',
            'ap_object' => 'lead/lead/act/take-from-call',
            'ap_action' => '(access)',
            'ap_action_json' => "[\"access\"]",
            'ap_effect' => 1,
            'ap_title' => 'Access to take lead in status pending from active call',
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
            'lead/lead/act/take-from-call',
        ]]);
        \Yii::$app->abac->invalidatePolicyCache();
    }
}
