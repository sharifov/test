<?php

use frontend\helpers\JsonHelper;
use modules\abac\src\entities\AbacPolicy;
use yii\db\Migration;

/**
 * Class m210615_135458_add_abac_rules_to_user_conversion
 */
class m210615_135458_add_abac_rules_to_user_conversion extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        if (!AbacPolicy::find()->where(['ap_object' => 'lead/lead/act/user-conversion'])->exists()) {
            $this->insert(
                '{{%abac_policy}}',
                [
                    'ap_rule_type' => 'p',
                    'ap_subject' => '("admin" in r.sub.env.user.roles)',
                    'ap_subject_json' => "{\"condition\":\"OR\",\"rules\":[{\"id\":\"env_user_roles\",\"field\":\"env.user.roles\",\"type\":\"string\",\"input\":\"select\",\"operator\":\"in_array\",\"value\":\"admin\"}],\"valid\":true}",
                    'ap_object' => 'lead/lead/act/user-conversion',
                    'ap_action' => '(delete)',
                    'ap_action_json' => "[\"delete\"]",
                    'ap_effect' => 1,
                    'ap_title' => 'User Conversion Delete',
                    'ap_sort_order' => 50,
                    'ap_enabled' => 1,
                ]
            );
            $this->insert(
                '{{%abac_policy}}',
                [
                    'ap_rule_type' => 'p',
                    'ap_subject' => '(r.sub.is_owner == true) || ("admin" in r.sub.env.user.roles)',
                    'ap_subject_json' => "{\"condition\":\"AND\",\"rules\":[{\"id\":\"lead/lead/is_owner\",\"field\":\"is_owner\",\"type\":\"boolean\",\"input\":\"radio\",\"operator\":\"==\",\"value\":true},{\"id\":\"env_user_roles\",\"field\":\"env.user.roles\",\"type\":\"string\",\"input\":\"select\",\"operator\":\"in_array\",\"value\":\"admin\"}],\"valid\":true}",
                    'ap_object' => 'lead/lead/act/user-conversion',
                    'ap_action' => '(read)',
                    'ap_action_json' => "[\"read\"]",
                    'ap_effect' => 1,
                    'ap_title' => 'User Conversion View List',
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
        AbacPolicy::deleteAll(['ap_object' => 'lead/lead/act/user-conversion']);
    }
}
