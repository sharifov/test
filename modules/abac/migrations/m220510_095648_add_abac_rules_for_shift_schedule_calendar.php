<?php

namespace modules\abac\migrations;

use modules\abac\src\entities\AbacPolicy;
use yii\db\Migration;

/**
 * Class m220510_095648_add_abac_rules_for_shift_schedule_calendar
 */
class m220510_095648_add_abac_rules_for_shift_schedule_calendar extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->insert('{{%abac_policy}}', [
            'ap_rule_type' => 'p',
            'ap_subject' => '("1" in r.sub.formSelectUserGroups || "2" in r.sub.formSelectUserGroups || "3" in r.sub.formSelectUserGroups || "4" in r.sub.formSelectUserGroups || "5" in r.sub.formSelectUserGroups || "6" in r.sub.formSelectUserGroups || "7" in r.sub.formSelectUserGroups || "8" in r.sub.formSelectUserGroups || "9" in r.sub.formSelectUserGroups || "10" in r.sub.formSelectUserGroups || "11" in r.sub.formSelectUserGroups || "12" in r.sub.formSelectUserGroups || "13" in r.sub.formSelectUserGroups || "14" in r.sub.formSelectUserGroups || "15" in r.sub.formSelectUserGroups || "17" in r.sub.formSelectUserGroups || "18" in r.sub.formSelectUserGroups || "19" in r.sub.formSelectUserGroups || "20" in r.sub.formSelectUserGroups || "21" in r.sub.formSelectUserGroups || "22" in r.sub.formSelectUserGroups || "23" in r.sub.formSelectUserGroups || "24" in r.sub.formSelectUserGroups || "25" in r.sub.formSelectUserGroups || "26" in r.sub.formSelectUserGroups) && (r.sub.env.available == true)',
            'ap_subject_json' => '{"condition":"AND","rules":[{"id":"shift/shift/formSelectUserGroups","field":"formSelectUserGroups","type":"string","input":"select","operator":"contains","value":["1","2","3","4","5","6","7","8","9","10","11","12","13","14","15","17","18","19","20","21","22","23","24","25","26"]},{"id":"env_available","field":"env.available","type":"boolean","input":"radio","operator":"==","value":true}],"valid":true}',
            'ap_object' => 'shift/shift/obj/user_shift_event',
            'ap_action' => '(access)',
            'ap_action_json' => json_encode(['access']),
            'ap_effect' => 1,
            'ap_title' => 'Available user groups in dropdown select in shift event form creation',
            'ap_sort_order' => 50,
            'ap_enabled' => 1,
            'ap_created_dt' => date('Y-m-d H:i:s'),
        ]);

        $this->insert('{{%abac_policy}}', [
            'ap_rule_type' => 'p',
            'ap_subject' => '("1" in r.sub.formSelectStatus || "2" in r.sub.formSelectStatus || "3" in r.sub.formSelectStatus || "6" in r.sub.formSelectStatus || "8" in r.sub.formSelectStatus) && (r.sub.env.available == true)',
            'ap_subject_json' => '{"condition":"AND","rules":[{"id":"shift/shift/formSelectStatus","field":"formSelectStatus","type":"string","input":"select","operator":"contains","value":["1","2","3","6","8"]},{"id":"env_available","field":"env.available","type":"boolean","input":"radio","operator":"==","value":true}],"valid":true}',
            'ap_object' => 'shift/shift/obj/user_shift_event',
            'ap_action' => '(access)',
            'ap_action_json' => json_encode(['access']),
            'ap_effect' => 1,
            'ap_title' => 'Available statuses in dropdown select in shift event form creation',
            'ap_sort_order' => 50,
            'ap_enabled' => 1,
            'ap_created_dt' => date('Y-m-d H:i:s'),
        ]);

        $this->insert('{{%abac_policy}}', [
            'ap_rule_type' => 'p',
            'ap_subject' => '("1" in r.sub.formSelectScheduleType || "2" in r.sub.formSelectScheduleType || "3" in r.sub.formSelectScheduleType || "4" in r.sub.formSelectScheduleType) && (r.sub.env.available == true)',
            'ap_subject_json' => '{"condition":"AND","rules":[{"id":"shift/shift/formSelectScheduleType","field":"formSelectScheduleType","type":"string","input":"select","operator":"contains","value":["1","2","3","4"]},{"id":"env_available","field":"env.available","type":"boolean","input":"radio","operator":"==","value":true}],"valid":true}',
            'ap_object' => 'shift/shift/obj/user_shift_event',
            'ap_action' => '(access)',
            'ap_action_json' => json_encode(['access']),
            'ap_effect' => 1,
            'ap_title' => 'Available schedule types in dropdown select in shift event form creation',
            'ap_sort_order' => 50,
            'ap_enabled' => 1,
            'ap_created_dt' => date('Y-m-d H:i:s'),
        ]);

        $this->insert('{{%abac_policy}}', [
            'ap_rule_type' => 'p',
            'ap_subject' => '(r.sub.env.available == true)',
            'ap_subject_json' => '{"condition":"AND","rules":[{"id":"env_available","field":"env.available","type":"boolean","input":"radio","operator":"==","value":true}],"valid":true}',
            'ap_object' => 'shift/shift/obj/user_shift_event',
            'ap_action' => '(delete)',
            'ap_action_json' => json_encode(['delete']),
            'ap_effect' => 1,
            'ap_title' => 'Access to delete event in calendar widget',
            'ap_sort_order' => 50,
            'ap_enabled' => 1,
            'ap_created_dt' => date('Y-m-d H:i:s'),
        ]);

        $this->insert('{{%abac_policy}}', [
            'ap_rule_type' => 'p',
            'ap_subject' => '(r.sub.env.available == true)',
            'ap_subject_json' => '{"condition":"AND","rules":[{"id":"env_available","field":"env.available","type":"boolean","input":"radio","operator":"==","value":true}],"valid":true}',
            'ap_object' => 'shift/shift/obj/user_shift_event',
            'ap_action' => '(createOnDoubleClick)',
            'ap_action_json' => json_encode(['createOnDoubleClick']),
            'ap_effect' => 1,
            'ap_title' => 'Access to create event by double click in calendar widget',
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
    }
}
