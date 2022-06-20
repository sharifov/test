<?php

namespace modules\abac\migrations;

use modules\abac\src\entities\AbacPolicy;
use yii\db\Migration;

/**
 * Class m220608_100005_update_abac_permissions_for_user_shift_calendar
 */
class m220608_100005_update_abac_permissions_for_user_shift_calendar extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        AbacPolicy::deleteAll(['AND', ['IN', 'ap_object', ['shift/shift/obj/user_shift_event',]], ['IN', 'ap_action', ['(createOnDoubleClick)']]]);
        AbacPolicy::deleteAll(['AND', ['IN', 'ap_object', ['shift/shift/obj/user_shift_calendar',]], ['IN', 'ap_action', ['(multiplePermanentlyDeleteEvents)']]]);
        AbacPolicy::deleteAll(['AND', ['IN', 'ap_object', ['shift/shift/obj/user_shift_calendar',]], ['IN', 'ap_action', ['(multipleUpdateEvents)']]]);
        AbacPolicy::deleteAll(['AND', ['IN', 'ap_object', ['shift/shift/obj/user_shift_calendar',]], ['IN', 'ap_action', ['(multipleDeleteEvents)']]]);

        AbacPolicy::updateAll([
            'ap_object' => 'shift/shift/obj/user_shift_event',
        ], [
            'AND',
            ['IN', 'ap_object', ['shift/shift/obj/user_shift_calendar']],
            ['IN', 'ap_action', ['(viewAllEvents)']]
        ]);

        AbacPolicy::updateAll([
            'ap_object' => 'shift/shift/obj/user_shift_event',
        ], [
            'AND',
            ['IN', 'ap_object', ['shift/shift/obj/user_shift_calendar']],
            ['IN', 'ap_action', ['(viewEventLogs)']]
        ]);

        AbacPolicy::updateAll([
            'ap_action' => '(softDelete)',
            'ap_action_json' => "[\"softDelete\"]"
        ], [
            'AND',
            ['IN', 'ap_object', ['shift/shift/obj/user_shift_event']],
            ['IN', 'ap_action', ['(delete)']]
        ]);

        AbacPolicy::updateAll([
            'ap_action' => '(delete)',
            'ap_action_json' => "[\"delete\"]"
        ], [
            'AND',
            ['IN', 'ap_object', ['shift/shift/obj/user_shift_event']],
            ['IN', 'ap_action', ['(permanentlyDelete)']]
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
