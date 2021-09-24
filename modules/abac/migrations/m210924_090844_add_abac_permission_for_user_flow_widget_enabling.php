<?php

namespace modules\abac\migrations;

use yii\db\Migration;

/**
 * Class m210924_090844_add_abac_permission_for_user_flow_widget_enabling
 */
class m210924_090844_add_abac_permission_for_user_flow_widget_enabling extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->insert('{{%abac_policy}}', [
            'ap_rule_type' => 'p',
            'ap_subject' => '("agent" in r.sub.env.user.roles) && (r.sub.env.dt.month == 10) && (r.sub.env.dt.year == 2021)',
            'ap_subject_json' => '{"condition":"AND","rules":[{"id":"env_user_roles","field":"env.user.roles","type":"string","input":"select","operator":"in_array","value":"agent"},{"id":"env_dt_month","field":"env.dt.month","type":"integer","input":"select","operator":"==","value":10},{"id":"env_dt_year","field":"env.dt.year","type":"integer","input":"number","operator":"==","value":2021}],"valid":true}',
            'ap_object' => 'frontend/widgets/ui/user-flow-widget',
            'ap_action' => '(include)',
            'ap_action_json' => "[\"include\"]",
            'ap_effect' => 1,
            'ap_title' => 'Include User Flow Widget in UI',
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
            'frontend/widgets/ui/user-flow-widget',
        ]]);
        \Yii::$app->abac->invalidatePolicyCache();
    }
}
