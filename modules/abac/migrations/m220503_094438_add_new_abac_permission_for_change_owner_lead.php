<?php

namespace modules\abac\migrations;

use modules\abac\src\entities\AbacPolicy;
use yii\db\Migration;

/**
 * Class m220503_094438_add_new_abac_permission_for_change_owner_lead
 */
class m220503_094438_add_new_abac_permission_for_change_owner_lead extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->insert('{{%abac_policy}}', [
            'ap_rule_type' => 'p',
            'ap_subject' => '(r.sub.status_id != 10) || ((r.sub.has_owner == false) && (r.sub.status_id == 10))',
            'ap_subject_json' => '{"condition":"OR","rules":[{"id":"lead/lead/status_id","field":"status_id","type":"integer","input":"select","operator":"!=","value":10},{"condition":"AND","rules":[{"id":"lead/lead/has_owner","field":"has_owner","type":"boolean","input":"radio","operator":"==","value":false},{"id":"lead/lead/status_id","field":"status_id","type":"integer","input":"select","operator":"==","value":10}]}],"valid":true}',
            'ap_object' => 'lead/lead/act/change-owner',
            'ap_action' => '(update)',
            'ap_action_json' => "[\"update\"]",
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
        if (AbacPolicy::deleteAll(['AND', ['IN', 'ap_object', ['lead/lead/act/change-owner',]], ['IN', 'ap_action', ['(update)']]])) {
            \Yii::$app->abac->invalidatePolicyCache();
        }
    }
}
