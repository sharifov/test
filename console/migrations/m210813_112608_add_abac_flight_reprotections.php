<?php

use modules\abac\src\entities\AbacPolicy;
use yii\db\Migration;

/**
 * Class m210813_112608_add_abac_flight_reprotections
 */
class m210813_112608_add_abac_flight_reprotections extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        if (!AbacPolicy::find()->where(['ap_object' => 'case/case/act/flight-reprotection-confirm'])->exists()) {
            $this->insert(
                '{{%abac_policy}}',
                [
                    'ap_rule_type' => 'p',
                    'ap_subject' => '(r.sub.is_owner == true) || ("admin" in r.sub.env.user.roles)',
                    'ap_subject_json' => "{\"condition\":\"OR\",\"rules\":[{\"id\":\"case/case/is_owner\",\"field\":\"is_owner\",\"type\":\"boolean\",\"input\":\"radio\",\"operator\":\"==\",\"value\":true},{\"id\":\"env_user_roles\",\"field\":\"env.user.roles\",\"type\":\"string\",\"input\":\"select\",\"operator\":\"in_array\",\"value\":\"admin\"}],\"valid\":true}",
                    'ap_object' => 'case/case/act/flight-reprotection-confirm',
                    'ap_action' => '(access)',
                    'ap_action_json' => "[\"access\"]",
                    'ap_effect' => 1,
                    'ap_title' => 'Flight Reprotection confirm',
                    'ap_sort_order' => 50,
                    'ap_enabled' => 1,
                ]
            );
        }
        if (!AbacPolicy::find()->where(['ap_object' => 'case/case/act/flight-reprotection-refund'])->exists()) {
            $this->insert(
                '{{%abac_policy}}',
                [
                    'ap_rule_type' => 'p',
                    'ap_subject' => '(r.sub.is_owner == true) || ("admin" in r.sub.env.user.roles)',
                    'ap_subject_json' => "{\"condition\":\"OR\",\"rules\":[{\"id\":\"case/case/is_owner\",\"field\":\"is_owner\",\"type\":\"boolean\",\"input\":\"radio\",\"operator\":\"==\",\"value\":true},{\"id\":\"env_user_roles\",\"field\":\"env.user.roles\",\"type\":\"string\",\"input\":\"select\",\"operator\":\"in_array\",\"value\":\"admin\"}],\"valid\":true}",
                    'ap_object' => 'case/case/act/flight-reprotection-refund',
                    'ap_action' => '(access)',
                    'ap_action_json' => "[\"access\"]",
                    'ap_effect' => 1,
                    'ap_title' => 'Flight Reprotection refund',
                    'ap_sort_order' => 50,
                    'ap_enabled' => 1,
                ]
            );
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        AbacPolicy::deleteAll(['ap_object' => 'case/case/act/flight-reprotection-confirm']);
        AbacPolicy::deleteAll(['ap_object' => 'case/case/act/flight-reprotection-refund']);
    }
}
