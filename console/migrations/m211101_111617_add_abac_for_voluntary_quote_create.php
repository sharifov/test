<?php

use modules\abac\src\entities\AbacPolicy;
use yii\db\Migration;

/**
 * Class m211101_111617_add_abac_for_voluntary_quote_create
 */
class m211101_111617_add_abac_for_voluntary_quote_create extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        if (!AbacPolicy::find()->where(['ap_object' => 'case/case/act/flight-voluntary-quote'])->exists()) {
            $this->insert('{{%abac_policy}}', [
                'ap_rule_type' => 'p',
                'ap_subject' => '("admin" in r.sub.env.user.roles)',
                'ap_subject_json' => '{"condition":"AND","rules":[{"id":"env_user_roles","field":"env.user.roles","type":"string","input":"select","operator":"in_array","value":"admin"}],"valid":true}',
                'ap_object' => 'case/case/act/flight-voluntary-quote',
                'ap_action' => '(create)',
                'ap_action_json' => "[\"create\"]",
                'ap_effect' => 1,
                'ap_title' => 'Access to create voluntary change quote from case/view',
                'ap_sort_order' => 50,
                'ap_enabled' => 1,
                'ap_created_dt' => date('Y-m-d H:i:s'),
            ]);
        }
        \Yii::$app->abac->invalidatePolicyCache();
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        AbacPolicy::deleteAll(['ap_object' => 'case/case/act/flight-voluntary-quote']);

        \Yii::$app->abac->invalidatePolicyCache();
    }
}
