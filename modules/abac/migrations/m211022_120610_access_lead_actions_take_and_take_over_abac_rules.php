<?php

namespace modules\abac\migrations;

use yii\db\Migration;

/**
 * Class m211022_120610_Access_Lead_Actions_Take_and_Take_Over_ABAC_rules
 */
class m211022_120610_access_lead_actions_take_and_take_over_abac_rules extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->delete('{{%abac_policy}}', ['IN', 'ap_object', [
            'lead/lead/act/take-lead',
        ]]);

        $this->insert('{{%abac_policy}}', [
            'ap_rule_type' => 'p',
            'ap_subject' => '((r.sub.status_id == 16) || (r.sub.status_id == 1) || (r.sub.status_id == 5) || (r.sub.status_id == 8)) && ((r.sub.is_owner == false) && ((r.sub.isInProject == true) && (r.sub.isInDepartment == true)))',
            'ap_subject_json' => '{"condition":"AND","rules":[{"condition":"OR","rules":[{"id":"lead/lead/status_id","field":"status_id","type":"integer","input":"select","operator":"==","value":16},{"id":"lead/lead/status_id","field":"status_id","type":"integer","input":"select","operator":"==","value":1},{"id":"lead/lead/status_id","field":"status_id","type":"integer","input":"select","operator":"==","value":5},{"id":"lead/lead/status_id","field":"status_id","type":"integer","input":"select","operator":"==","value":8}]},{"condition":"AND","rules":[{"id":"lead/lead/is_owner","field":"is_owner","type":"boolean","input":"radio","operator":"==","value":false},{"condition":"AND","rules":[{"id":"lead/lead/isInProject","field":"isInProject","type":"boolean","input":"radio","operator":"==","value":true},{"id":"lead/lead/isInDepartment","field":"isInDepartment","type":"boolean","input":"radio","operator":"==","value":true}]}]}],"valid":true}',
            'ap_object' => 'lead/lead/act/take-lead',
            'ap_action' => '(access)',
            'ap_action_json' => "[\"access\"]",
            'ap_effect' => 1,
            'ap_title' => 'Take Lead action',
            'ap_sort_order' => 50,
            'ap_enabled' => 1,
            'ap_created_dt' => date('Y-m-d H:i:s'),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m211022_120610_access_lead_actions_take_and_take_over_abac_rules cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m211022_120610_Access_Lead_Actions_Take_and_Take_Over_ABAC_rules cannot be reverted.\n";

        return false;
    }
    */
}
