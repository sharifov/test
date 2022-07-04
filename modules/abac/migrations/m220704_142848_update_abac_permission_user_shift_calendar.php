<?php

namespace modules\abac\migrations;

use modules\abac\src\AbacService;
use yii\db\Migration;

/**
 * Class m220704_142848_update_abac_permission_user_shift_calendar
 */
class m220704_142848_update_abac_permission_user_shift_calendar extends Migration
{
    private const AP_SUBJECT = '("admin" in r.sub.env.user.roles)';
    private const AP_OBJECT = 'shift/shift/obj/user_shift_calendar';
    private const AP_ACTION = '(access)';
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
        $this->update(
            '{{%abac_policy}}',
            ['ap_hash_code' => AbacService::generateHashCode(self::GENERATE_HASH_DATA)],
            ['AND', ['IN', 'ap_object', [self::AP_OBJECT]], ['IN', 'ap_action', [self::AP_ACTION]], ['ap_enabled' => 1], ['ap_effect' => 1]]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        return false;
    }
}
