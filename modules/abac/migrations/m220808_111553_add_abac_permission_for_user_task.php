<?php

namespace modules\abac\migrations;

use modules\abac\src\AbacService;
use modules\abac\src\entities\AbacMigration;
use modules\abac\src\entities\AbacPolicy;
use Yii;

/**
 * Class m220808_111553_add_abac_permission_for_user_task
 */
class m220808_111553_add_abac_permission_for_user_task extends AbacMigration
{
    private const AP_SUBJECT = '(r.sub.isUserTaskOwner == true) || ("admin" in r.sub.env.user.roles)';
    private const AP_OBJECT = 'task-list/task-list/obj/user_task';
    private const AP_ACTION = '(read)';
    private const AP_EFFECT = 1;
    private const AP_SUBJECT_JSON = '{"condition":"OR","rules":[{"id":"task-list/task-list/isUserTaskOwner","field":"isUserTaskOwner","type":"boolean","input":"radio","operator":"==","value":true},{"id":"env_user_roles","field":"env.user.roles","type":"string","input":"select","operator":"in_array","value":"admin"}],"not":false,"valid":true}';

    private const GENERATE_HASH_DATA = [
        self::AP_OBJECT,
        self::AP_ACTION,
        self::AP_SUBJECT,
        self::AP_EFFECT,
    ];
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->insert('{{%abac_policy}}', [
            'ap_rule_type' => 'p',
            'ap_subject' => self::AP_SUBJECT,
            'ap_subject_json' => self::AP_SUBJECT_JSON,
            'ap_object' => self::AP_OBJECT,
            'ap_action' => self::AP_ACTION,
            'ap_action_json' => "[\"read\"]",
            'ap_effect' => self::AP_EFFECT,
            'ap_title' => 'Access to view UserTask details',
            'ap_hash_code' => AbacService::generateHashCode(self::GENERATE_HASH_DATA),
            'ap_sort_order' => 50,
            'ap_enabled' => 1,
            'ap_created_dt' => date('Y-m-d H:i:s'),
        ]);

        Yii::$app->abac->invalidatePolicyCache();
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        if (AbacPolicy::deleteAll(['ap_object' => self::AP_OBJECT, 'ap_action' => self::AP_ACTION])) {
            Yii::$app->abac->invalidatePolicyCache();
        }
    }
}
