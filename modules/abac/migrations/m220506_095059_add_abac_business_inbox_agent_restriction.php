<?php

namespace modules\abac\migrations;

use modules\abac\src\entities\AbacPolicy;
use yii\db\Migration;

/**
 * Class m220506_095059_add_abac_business_inbox_agent_restriction
 */
class m220506_095059_add_abac_business_inbox_agent_restriction extends Migration
{
    private string $apObject = 'lead/lead/queue/business_inbox/query/listing';

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        if (!AbacPolicy::find()->where(['ap_object' => $this->apObject])->exists()) {
            $this->insert('{{%abac_policy}}', [
                'ap_rule_type' => 'p',
                'ap_subject' => '("agent" in r.sub.env.user.roles)',
                'ap_subject_json' => '{"condition":"AND","rules":[{"id":"env_user_roles","field":"env.user.roles","type":"string","input":"select","operator":"in_array","value":"agent"}],"valid":true}',
                'ap_object' => $this->apObject,
                'ap_action' => '(read_wt_user_restriction)',
                'ap_action_json' => json_encode(['read_wt_user_restriction']),
                'ap_effect' => 1,
                'ap_title' => 'Add Employee restriction for business inbox lead listing',
                'ap_sort_order' => 50,
                'ap_enabled' => 1,
                'ap_created_dt' => date('Y-m-d H:i:s'),
            ]);
            \Yii::$app->abac->invalidatePolicyCache();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        if (AbacPolicy::deleteAll(['IN', 'ap_object', [$this->apObject]])) {
            \Yii::$app->abac->invalidatePolicyCache();
        }
    }
}
