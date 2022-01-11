<?php

namespace modules\abac\migrations;

use yii\db\Migration;

/**
 * Class m220111_105056_update_abac_rules_for_cases_queues_sql_query
 */
class m220111_105056_update_abac_rules_for_cases_queues_sql_query extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->delete('{{%abac_policy}}', ['AND', ['IN', 'ap_object', [
            'case/case/sql/queue',
        ]], ['IN', 'ap_action', ['(ownerAccess)']]]);

        $this->insert('{{%abac_policy}}', [
            'ap_rule_type' => 'p',
            'ap_subject' => '(r.sub.env.available == true) && (((r.sub.env.req.action == "cases-q/need-action") || (r.sub.mainMenuCaseBadgeName == "need_action")) || ((r.sub.env.req.action == "cases-q/pending") || (r.sub.mainMenuCaseBadgeName == "pending")) || ((r.sub.env.req.action == "cases-q/inbox") || (r.sub.mainMenuCaseBadgeName == "inbox")) || ((r.sub.env.req.action == "cases-q/unidentified") || (r.sub.mainMenuCaseBadgeName == "unidentified")) || ((r.sub.env.req.action == "cases-q/first-priority") || (r.sub.mainMenuCaseBadgeName == "first-priority")) || ((r.sub.env.req.action == "cases-q/second-priority") || (r.sub.mainMenuCaseBadgeName == "second-priority")) || ((r.sub.env.req.action == "cases-q/pass-departure") || (r.sub.mainMenuCaseBadgeName == "pass-departure")) || ((r.sub.env.req.action == "cases-q/processing") || (r.sub.mainMenuCaseBadgeName == "processing")) || ((r.sub.env.req.action == "cases-q/follow-up") || (r.sub.mainMenuCaseBadgeName == "follow-up")) || ((r.sub.env.req.action == "cases-q/awaiting") || (r.sub.mainMenuCaseBadgeName == "awaiting")) || ((r.sub.env.req.action == "cases-q/auto-processing") || (r.sub.mainMenuCaseBadgeName == "auto-processing")) || (r.sub.env.req.action == "cases-q/solved") || ((r.sub.env.req.action == "cases-q/error") || (r.sub.mainMenuCaseBadgeName == "error")) || ((r.sub.env.req.action == "cases-q/trash") || (r.sub.mainMenuCaseBadgeName == "trash")))',
            'ap_subject_json' => '{"condition":"AND","rules":[{"id":"env_available","field":"env.available","type":"boolean","input":"radio","operator":"==","value":true},{"condition":"OR","rules":[{"condition":"OR","rules":[{"id":"env_action","field":"env.req.action","type":"string","input":"text","operator":"==","value":"cases-q/need-action"},{"id":"case/case/mainMenuCaseBadgeName","field":"mainMenuCaseBadgeName","type":"string","input":"select","operator":"==","value":"need_action"}]},{"condition":"OR","rules":[{"id":"env_action","field":"env.req.action","type":"string","input":"text","operator":"==","value":"cases-q/pending"},{"id":"case/case/mainMenuCaseBadgeName","field":"mainMenuCaseBadgeName","type":"string","input":"select","operator":"==","value":"pending"}]},{"condition":"OR","rules":[{"id":"env_action","field":"env.req.action","type":"string","input":"text","operator":"==","value":"cases-q/inbox"},{"id":"case/case/mainMenuCaseBadgeName","field":"mainMenuCaseBadgeName","type":"string","input":"select","operator":"==","value":"inbox"}]},{"condition":"OR","rules":[{"id":"env_action","field":"env.req.action","type":"string","input":"text","operator":"==","value":"cases-q/unidentified"},{"id":"case/case/mainMenuCaseBadgeName","field":"mainMenuCaseBadgeName","type":"string","input":"select","operator":"==","value":"unidentified"}]},{"condition":"OR","rules":[{"id":"env_action","field":"env.req.action","type":"string","input":"text","operator":"==","value":"cases-q/first-priority"},{"id":"case/case/mainMenuCaseBadgeName","field":"mainMenuCaseBadgeName","type":"string","input":"select","operator":"==","value":"first-priority"}]},{"condition":"OR","rules":[{"id":"env_action","field":"env.req.action","type":"string","input":"text","operator":"==","value":"cases-q/second-priority"},{"id":"case/case/mainMenuCaseBadgeName","field":"mainMenuCaseBadgeName","type":"string","input":"select","operator":"==","value":"second-priority"}]},{"condition":"OR","rules":[{"id":"env_action","field":"env.req.action","type":"string","input":"text","operator":"==","value":"cases-q/pass-departure"},{"id":"case/case/mainMenuCaseBadgeName","field":"mainMenuCaseBadgeName","type":"string","input":"select","operator":"==","value":"pass-departure"}]},{"condition":"OR","rules":[{"id":"env_action","field":"env.req.action","type":"string","input":"text","operator":"==","value":"cases-q/processing"},{"id":"case/case/mainMenuCaseBadgeName","field":"mainMenuCaseBadgeName","type":"string","input":"select","operator":"==","value":"processing"}]},{"condition":"OR","rules":[{"id":"env_action","field":"env.req.action","type":"string","input":"text","operator":"==","value":"cases-q/follow-up"},{"id":"case/case/mainMenuCaseBadgeName","field":"mainMenuCaseBadgeName","type":"string","input":"select","operator":"==","value":"follow-up"}]},{"condition":"OR","rules":[{"id":"env_action","field":"env.req.action","type":"string","input":"text","operator":"==","value":"cases-q/awaiting"},{"id":"case/case/mainMenuCaseBadgeName","field":"mainMenuCaseBadgeName","type":"string","input":"select","operator":"==","value":"awaiting"}]},{"condition":"OR","rules":[{"id":"env_action","field":"env.req.action","type":"string","input":"text","operator":"==","value":"cases-q/auto-processing"},{"id":"case/case/mainMenuCaseBadgeName","field":"mainMenuCaseBadgeName","type":"string","input":"select","operator":"==","value":"auto-processing"}]},{"id":"env_action","field":"env.req.action","type":"string","input":"text","operator":"==","value":"cases-q/solved"},{"condition":"OR","rules":[{"id":"env_action","field":"env.req.action","type":"string","input":"text","operator":"==","value":"cases-q/error"},{"id":"case/case/mainMenuCaseBadgeName","field":"mainMenuCaseBadgeName","type":"string","input":"select","operator":"==","value":"error"}]},{"condition":"OR","rules":[{"id":"env_action","field":"env.req.action","type":"string","input":"text","operator":"==","value":"cases-q/trash"},{"id":"case/case/mainMenuCaseBadgeName","field":"mainMenuCaseBadgeName","type":"string","input":"select","operator":"==","value":"trash"}]}]}],"valid":true}',
            'ap_object' => 'case/case/sql/queue',
            'ap_action' => '(ownerAccess)',
            'ap_action_json' => "[\"ownerAccess\"]",
            'ap_effect' => 1,
            'ap_title' => '',
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
        $this->delete('{{%abac_policy}}', ['AND', ['IN', 'ap_object', [
            'case/case/sql/queue',
        ]], ['IN', 'ap_action', ['(ownerAccess)']]]);

        \Yii::$app->abac->invalidatePolicyCache();
    }
}
