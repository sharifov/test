<?php

namespace modules\abac\migrations;

use yii\db\Migration;

/**
 * Class m220504_100547_update_abac_rules_communication_block_view
 */
class m220504_100547_update_abac_rules_communication_block_view extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->update(
            '{{%abac_policy}}',
            ['ap_enabled' => 0],
            ['AND', ['IN', 'ap_object', ['lead/lead/communication_block',]], ['IN', 'ap_action', ['(view)']], ['ap_enabled' => 1]]
        );

        $this->insert('{{%abac_policy}}', [
            'ap_rule_type' => 'p',
            'ap_subject' => '(("agent" not in r.sub.env.user.roles)) || ((r.sub.is_owner == true) && ("agent" in r.sub.env.user.roles))',
            'ap_subject_json' => '{"condition":"OR","rules":[{"condition":"AND","rules":[{"id":"env_user_roles","field":"env.user.roles","type":"string","input":"select","operator":"not_in_array","value":"agent"}]},{"condition":"AND","rules":[{"id":"lead/lead/is_owner","field":"is_owner","type":"boolean","input":"radio","operator":"==","value":true},{"id":"env_user_roles","field":"env.user.roles","type":"string","input":"select","operator":"in_array","value":"agent"}]}],"valid":true}',
            'ap_object' => 'lead/lead/communication_block',
            'ap_action' => '(view)',
            'ap_action_json' => "[\"view\"]",
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
            ['AND', ['IN', 'ap_object', ['lead/lead/communication_block']], ['IN', 'ap_action', ['(view)']], ['ap_enabled' => 1]]
        );

        $this->update(
            '{{%abac_policy}}',
            ['ap_enabled' => 1],
            [
                'AND',
                ['IN', 'ap_object', ['lead/lead/communication_block',]],
                ['IN', 'ap_action', ['(view)']],
                ['ap_enabled' => 0]
            ]
        );

        \Yii::$app->abac->invalidatePolicyCache();
    }
}
