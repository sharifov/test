<?php

namespace modules\abac\migrations;

use Yii;
use yii\db\Migration;

/**
 * Class m220420_144302_update_abac_rules_refund_and_change
 */
class m220420_144302_update_abac_rules_refund_and_change extends Migration
{
    /**
     * @return void
     */
    public function safeUp(): void
    {
        // empty, because execution be moved to corrective migration
        // path: @root/modules/abac/migration/m220429_065150_correct_update_abac_rules_refund_and_change.php
    }

    /**
     * @return void
     */
    public function safeDown(): void
    {
        // nothing
    }
}
