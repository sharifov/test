<?php

use modules\abac\src\entities\AbacPolicy;
use yii\db\Migration;

/**
 * Class m220202_160305_add_communication_block_abac_policy
 */
class m220202_160305_add_communication_block_abac_policy extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        if (!AbacPolicy::find()->where(['ap_object' => 'lead/lead/communication_block'])->exists()) {
            $this->insert('{{%abac_policy}}', [
                'ap_rule_type' => 'p',
                'ap_subject' => '(r.sub.env.available == true)',
                'ap_subject_json' => "{\"condition\":\"AND\",\"rules\":[{\"id\":\"env_available\",\"field\":\"env.available\",\"type\":\"boolean\",\"input\":\"radio\",\"operator\":\"==\",\"value\":true}],\"valid\":true}",
                'ap_object' => 'lead/lead/communication_block',
                'ap_action' => '(view)',
                'ap_action_json' => "[\"view\"]",
                'ap_effect' => 1,
                'ap_title' => 'View Lead communication block',
                'ap_sort_order' => 50,
                'ap_enabled' => 1,
                'ap_created_dt' => date('Y-m-d H:i:s'),
            ]);
            $this->insert('{{%abac_policy}}', [
                'ap_rule_type' => 'p',
                'ap_subject' => '(r.sub.project_sms_enable == false)',
                'ap_subject_json' => "{\"condition\":\"AND\",\"rules\":[{\"id\":\"lead/lead/communication_block/project_sms_enable\",\"field\":\"project_sms_enable\",\"type\":\"boolean\",\"input\":\"radio\",\"operator\":\"==\",\"value\":false}],\"valid\":true}",
                'ap_object' => 'lead/lead/communication_block',
                'ap_action' => '(sendSms)',
                'ap_action_json' => "[\"sendSms\"]",
                'ap_effect' => 0,
                'ap_title' => 'Deny send sms from communication block',
                'ap_sort_order' => 49,
                'ap_enabled' => 1,
                'ap_created_dt' => date('Y-m-d H:i:s'),
            ]);
            $this->insert('{{%abac_policy}}', [
                'ap_rule_type' => 'p',
                'ap_subject' => '(r.sub.client_is_unsubscribe == false) && (("admin" in r.sub.env.user.roles) || (r.sub.is_owner == true))',
                'ap_subject_json' => "{\"condition\":\"AND\",\"rules\":[{\"id\":\"lead/lead/client_is_unsubscribe\",\"field\":\"client_is_unsubscribe\",\"type\":\"boolean\",\"input\":\"radio\",\"operator\":\"==\",\"value\":false},{\"condition\":\"OR\",\"rules\":[{\"id\":\"env_user_roles\",\"field\":\"env.user.roles\",\"type\":\"string\",\"input\":\"select\",\"operator\":\"in_array\",\"value\":\"admin\"},{\"id\":\"lead/lead/is_owner\",\"field\":\"is_owner\",\"type\":\"boolean\",\"input\":\"radio\",\"operator\":\"==\",\"value\":true}]}],\"valid\":true}",
                'ap_object' => 'lead/lead/communication_block',
                'ap_action' => '(sendSms)|(sendEmail)|(makeCall)',
                'ap_action_json' => "[\"sendSms\",\"sendEmail\",\"makeCall\"]",
                'ap_effect' => 1,
                'ap_title' => 'Allow all actions fro communication block',
                'ap_sort_order' => 50,
                'ap_enabled' => 1,
                'ap_created_dt' => date('Y-m-d H:i:s'),
            ]);
        }
        if (!AbacPolicy::find()->where(['ap_object' => 'case/case/communication_block'])->exists()) {
            $this->insert('{{%abac_policy}}', [
                'ap_rule_type' => 'p',
                'ap_subject' => '(r.sub.env.available == true)',
                'ap_subject_json' => "{\"condition\":\"AND\",\"rules\":[{\"id\":\"env_available\",\"field\":\"env.available\",\"type\":\"boolean\",\"input\":\"radio\",\"operator\":\"==\",\"value\":true}],\"valid\":true}",
                'ap_object' => 'case/case/communication_block',
                'ap_action' => '(view)',
                'ap_action_json' => "[\"view\"]",
                'ap_effect' => 1,
                'ap_title' => 'View Case communication block',
                'ap_sort_order' => 50,
                'ap_enabled' => 1,
                'ap_created_dt' => date('Y-m-d H:i:s'),
            ]);
            $this->insert('{{%abac_policy}}', [
                'ap_rule_type' => 'p',
                'ap_subject' => '(r.sub.project_sms_enable == false)',
                'ap_subject_json' => "{\"condition\":\"AND\",\"rules\":[{\"id\":\"case/case/communication_block/project_sms_enable\",\"field\":\"project_sms_enable\",\"type\":\"boolean\",\"input\":\"radio\",\"operator\":\"==\",\"value\":false}],\"valid\":true}",
                'ap_object' => 'case/case/communication_block',
                'ap_action' => '(sendSms)',
                'ap_action_json' => "[\"sendSms\"]",
                'ap_effect' => 0,
                'ap_title' => 'Deny send sms from communication block',
                'ap_sort_order' => 49,
                'ap_enabled' => 1,
                'ap_created_dt' => date('Y-m-d H:i:s'),
            ]);
            $this->insert('{{%abac_policy}}', [
                'ap_rule_type' => 'p',
                'ap_subject' => '("admin" in r.sub.env.user.roles) || (r.sub.is_owner == true)',
                'ap_subject_json' => "{\"condition\":\"OR\",\"rules\":[{\"id\":\"env_user_roles\",\"field\":\"env.user.roles\",\"type\":\"string\",\"input\":\"select\",\"operator\":\"in_array\",\"value\":\"admin\"},{\"id\":\"case/case/is_owner\",\"field\":\"is_owner\",\"type\":\"boolean\",\"input\":\"radio\",\"operator\":\"==\",\"value\":true}],\"valid\":true}",
                'ap_object' => 'case/case/communication_block',
                'ap_action' => '(sendSms)|(sendEmail)|(makeCall)',
                'ap_action_json' => "[\"sendSms\",\"sendEmail\",\"makeCall\"]",
                'ap_effect' => 1,
                'ap_title' => 'Allow all actions fro communication block',
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
        AbacPolicy::deleteAll(['ap_object' => 'lead/lead/communication_block']);
        AbacPolicy::deleteAll(['ap_object' => 'case/case/communication_block']);
        \Yii::$app->abac->invalidatePolicyCache();
    }
}
