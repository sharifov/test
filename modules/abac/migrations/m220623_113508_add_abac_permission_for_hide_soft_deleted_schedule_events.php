<?php

namespace modules\abac\migrations;

use modules\abac\src\AbacService;
use modules\abac\src\entities\AbacPolicy;
use yii\db\Migration;

/**
 * Class m220623_113508_add_abac_permission_for_hide_soft_deleted_schedule_events
 */
class m220623_113508_add_abac_permission_for_hide_soft_deleted_schedule_events extends Migration
{
    private const AP_SUBJECT = '("agent" in r.sub.env.user.roles) || ("shift_supervisor" in r.sub.env.user.roles)';
    private const AP_OBJECT = 'shift/shift/obj/user_shift_event';
    private const AP_ACTION = '(hideSoftDeletedEvents)';
    private const AP_EFFECT = 1;

    private const GENERATE_HASH_DATA = [
        self::AP_OBJECT,
        self::AP_ACTION,
        self::AP_SUBJECT,
        self::AP_EFFECT
    ];
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->insert('{{%abac_policy}}', [
            'ap_rule_type' => 'p',
            'ap_subject' => self::AP_SUBJECT,
            'ap_subject_json' => '{"condition":"OR","rules":[{"id":"env_user_roles","field":"env.user.roles","type":"string","input":"select","operator":"in_array","value":"agent"},{"id":"env_user_roles","field":"env.user.roles","type":"string","input":"select","operator":"in_array","value":"shift_supervisor"}],"not":false,"valid":true}',
            'ap_object' => self::AP_OBJECT,
            'ap_action' => self::AP_ACTION,
            'ap_action_json' => "[\"hideSoftDeletedEvents\"]",
            'ap_effect' => self::AP_EFFECT,
            'ap_title' => 'Hide Soft Deleted Schedule Events',
            'ap_sort_order' => 50,
            'ap_hash_code' => AbacService::generateHashCode(self::GENERATE_HASH_DATA),
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
        if (AbacPolicy::deleteAll(['AND', ['IN', 'ap_object', [self::AP_OBJECT]], ['IN', 'ap_action', [self::AP_ACTION]]])) {
            \Yii::$app->abac->invalidatePolicyCache();
        }
    }
}
