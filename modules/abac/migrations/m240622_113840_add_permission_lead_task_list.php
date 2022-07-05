<?php

namespace modules\abac\migrations;

use modules\abac\src\AbacService;
use modules\abac\src\entities\AbacPolicy;
use yii\db\Migration;

/**
 * Class m240622_113840_add_permission_lead_task_list
 */
class m240622_113840_add_permission_lead_task_list extends Migration
{
    private const AP_SUBJECT = '(hasActiveLeadObjectSegment == true) && (has_owner == true) && (statusId in [1,2,16])';
    private const AP_OBJECT = 'lead/lead/task_list/assign_task';
    private const AP_ACTION = '(access)';
    private const AP_EFFECT = 1;
    private const AP_SUBJECT_JSON = '{"condition":"AND","rules":[{"id":"lead/lead/task_list/hasActiveLeadObjectSegment","field":"hasActiveLeadObjectSegment","type":"boolean","input":"radio","operator":"==","value":true},{"id":"lead/lead/has_owner","field":"has_owner","type":"boolean","input":"radio","operator":"==","value":true},{"id":"lead/lead/task_list/statusId","field":"statusId","type":"integer","input":"select","operator":"in","value":[1,2,16]}],"not":false,"valid":true}';

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
            'ap_action_json' => "[\"access\"]",
            'ap_effect' => self::AP_EFFECT,
            'ap_title' => 'Access to assign task list to lead',
            'ap_hash_code' => AbacService::generateHashCode(self::GENERATE_HASH_DATA),
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
        if (AbacPolicy::deleteAll(['ap_object' => self::AP_OBJECT, 'ap_action' => self::AP_ACTION])) {
            \Yii::$app->abac->invalidatePolicyCache();
        }
    }
}
