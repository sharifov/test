<?php

namespace modules\abac\migrations;

use modules\abac\src\entities\AbacPolicy;
use yii\db\Migration;

/**
 * Class m220211_122924_add_abac_permissions_for_lpp
 */
class m220211_122924_add_abac_permissions_for_lpp extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->alterColumn('{{%abac_policy}}', 'ap_action', $this->string(1000));

        if (!AbacPolicy::find()->where(['ap_object' => 'lead/poor_processing/rule'])->exists()) {
            $this->insert('{{%abac_policy}}', [
                'ap_rule_type' => 'p',
                'ap_subject' => '("admin" in r.sub.env.user.roles)',
                'ap_subject_json' => '{"condition":"AND","rules":[{"id":"env_user_roles","field":"env.user.roles","type":"string","input":"select","operator":"in_array","value":"admin"},{"condition":"AND","rules":[{"id":"lead/poor_processing/leadProjectId","field":"leadProjectId","type":"integer","input":"select","operator":"not_in","value":[7]},{"id":"lead/poor_processing/leadTypeId","field":"leadTypeId","type":"integer","input":"select","operator":"not_in","value":[2,3]},{"id":"lead/poor_processing/leadStatusId","field":"leadStatusId","type":"integer","input":"select","operator":"in","value":[2]}]}],"valid":true}',
                'ap_object' => 'lead/poor_processing/rule',
                'ap_action' => '(last_action)|(no_action)|(extra_to_processing_take)|(extra_to_processing_multiple_update)|(scheduled_communication)|(expert_idle)|(send_sms_offer)',
                'ap_action_json' => "[\"last_action\",\"no_action\",\"extra_to_processing_take\",\"extra_to_processing_multiple_update\",\"scheduled_communication\",\"expert_idle\",\"send_sms_offer\"]",
                'ap_effect' => 1,
                'ap_title' => 'Access Lead Poor Processing Rules',
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
        $this->delete('{{%abac_policy}}', ['IN', 'ap_object', [
            'lead/poor_processing/rule',
        ]]);
        \Yii::$app->abac->invalidatePolicyCache();
    }
}
