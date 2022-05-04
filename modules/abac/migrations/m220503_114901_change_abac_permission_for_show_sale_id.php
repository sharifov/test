<?php

namespace modules\abac\migrations;

use yii\db\Migration;

/**
 * Class m220503_114901_change_abac_permission_for_show_sale_id
 */
class m220503_114901_change_abac_permission_for_show_sale_id extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->update(
            '{{%abac_policy}}',
            ['ap_enabled' => 0],
            ['AND', ['IN', 'ap_object', ['case/case/ui/sale-id',]], ['IN', 'ap_action', ['(read)']], ['ap_enabled' => 1]]
        );

        $this->insert('{{%abac_policy}}', [
            'ap_rule_type' => 'p',
            'ap_subject' => '("sup_agent" not in r.sub.env.user.roles) && ("supervision" not in r.sub.env.user.roles)',
            'ap_subject_json' => '{"condition":"AND","rules":[{"id":"env_user_roles","field":"env.user.roles","type":"string","input":"select","operator":"not_in_array","value":"sup_agent"},{"id":"env_user_roles","field":"env.user.roles","type":"string","input":"select","operator":"not_in_array","value":"supervision"}],"valid":true}',
            'ap_object' => 'case/case/ui/sale-id',
            'ap_action' => '(read)',
            'ap_action_json' => "[\"read\"]",
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
        $this->delete(
            '{{%abac_policy}}',
            ['AND', ['IN', 'ap_object', ['case/case/ui/sale-id']], ['IN', 'ap_action', ['(read)']], ['ap_enabled' => 1]]
        );

        $this->update(
            '{{%abac_policy}}',
            ['ap_enabled' => 1],
            [
                'AND',
                ['IN', 'ap_object', ['case/case/ui/sale-id',]],
                ['IN', 'ap_action', ['(read)']],
                ['ap_enabled' => 0]
            ]
        );

        \Yii::$app->abac->invalidatePolicyCache();
    }
}
