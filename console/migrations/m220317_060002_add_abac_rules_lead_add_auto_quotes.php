<?php

use modules\abac\src\entities\AbacPolicy;
use yii\db\Migration;

/**
 * Class m220317_060002_add_abac_rules_lead_add_auto_quotes
 */
class m220317_060002_add_abac_rules_lead_add_auto_quotes extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        if (!AbacPolicy::find()->where(['ap_object' => 'lead/lead/act/auto-add-quotes'])->exists()) {
            $this->insert(
                '{{%abac_policy}}',
                [
                    'ap_rule_type' => 'p',
                    'ap_subject' => '((r.sub.quotesCount == 0) && (r.sub.flightSegmentsCount > 0)) && (("admin" in r.sub.env.user.roles) || ("agent" in r.sub.env.user.roles))',
                    'ap_subject_json' => '{"condition":"AND","rules":[{"condition":"AND","rules":[{"id":"lead/lead/quotesCount","field":"quotesCount","type":"integer","input":"text","operator":"==","value":0},{"id":"lead/lead/flightSegmentsCount","field":"flightSegmentsCount","type":"integer","input":"text","operator":">","value":0}]},{"condition":"OR","rules":[{"id":"env_user_roles","field":"env.user.roles","type":"string","input":"select","operator":"in_array","value":"admin"},{"id":"env_user_roles","field":"env.user.roles","type":"string","input":"select","operator":"in_array","value":"agent"}]}],"valid":true}',
                    'ap_object' => 'lead/lead/act/auto-add-quotes',
                    'ap_action' => '(access)',
                    'ap_action_json' => "[\"access\"]",
                    'ap_effect' => 1,
                    'ap_title' => 'Lead - auto add  quotes',
                    'ap_sort_order' => 50,
                    'ap_enabled' => 1,
                ]
            );
            \Yii::$app->abac->invalidatePolicyCache();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        if (AbacPolicy::deleteAll(['ap_object' => 'lead/lead/act/auto-add-quotes'])) {
            \Yii::$app->abac->invalidatePolicyCache();
        }
    }
}
