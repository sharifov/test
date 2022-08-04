<?php

namespace modules\abac\migrations;

use modules\abac\src\AbacService;
use modules\abac\src\entities\AbacMigration;
use yii\db\Migration;

/**
 * Class m220727_083339_change_abac_permission_to_qa_list
 */
class m220727_083339_change_abac_permission_to_qa_list extends AbacMigration
{
    private const AP_SUBJECT = '(r.sub.closeReason == "alternative") || (r.sub.closeReason == "invalid") || (r.sub.closeReason == "transfer") || (r.sub.closeReason == "client_needs_no_sales") || (r.sub.closeReason == "duplicated") || (r.sub.closeReason == "proper_follow_up_done_never_answered")';
    private const AP_OBJECT = 'lead/lead/obj/lead';
    private const AP_ACTION = '(toQaList)';
    private const AP_EFFECT = 1;
    private const AP_SUBJECT_JSON = '{"condition":"OR","rules":[{"id":"lead/lead/close_reason","field":"closeReason","type":"string","input":"select","operator":"==","value":"alternative"},{"id":"lead/lead/close_reason","field":"closeReason","type":"string","input":"select","operator":"==","value":"invalid"},{"id":"lead/lead/close_reason","field":"closeReason","type":"string","input":"select","operator":"==","value":"transfer"},{"id":"lead/lead/close_reason","field":"closeReason","type":"string","input":"select","operator":"==","value":"client_needs_no_sales"},{"id":"lead/lead/close_reason","field":"closeReason","type":"string","input":"select","operator":"==","value":"duplicated"},{"id":"lead/lead/close_reason","field":"closeReason","type":"string","input":"select","operator":"==","value":"proper_follow_up_done_never_answered"}],"not":false,"valid":true}';

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
        $this->update(
            '{{%abac_policy}}',
            ['ap_enabled' => 0],
            ['AND', ['IN', 'ap_object', [self::AP_OBJECT]], ['IN', 'ap_action', [self::AP_ACTION]], ['ap_enabled' => 1],['ap_effect' => self::AP_EFFECT]]
        );

        $this->insert('{{%abac_policy}}', [
            'ap_rule_type' => 'p',
            'ap_subject' => self::AP_SUBJECT,
            'ap_subject_json' => self::AP_SUBJECT_JSON,
            'ap_object' => self::AP_OBJECT,
            'ap_action' => self::AP_ACTION,
            'ap_action_json' => "[\"toQaList\"]",
            'ap_effect' => self::AP_EFFECT,
            'ap_title' => 'Access to move lead to qa',
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
        $this->delete(
            '{{%abac_policy}}',
            ['AND', ['IN', 'ap_object', [self::AP_OBJECT]], ['IN', 'ap_action', [self::AP_ACTION]], ['ap_enabled' => 1], ['ap_effect' => self::AP_EFFECT]]
        );

        $this->update(
            '{{%abac_policy}}',
            ['ap_enabled' => 1],
            [
                'AND',
                ['IN', 'ap_object', [self::AP_OBJECT]],
                ['IN', 'ap_action', [self::AP_ACTION]],
                ['ap_enabled' => 0],
                ['ap_effect' => self::AP_EFFECT]
            ]
        );

        \Yii::$app->abac->invalidatePolicyCache();
    }
}
