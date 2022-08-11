<?php

use modules\abac\src\AbacService;
use modules\abac\src\entities\AbacMigration;
use modules\abac\src\entities\AbacPolicy;
use yii\db\Query;

/**
 * Class m220728_095905_add_abac_permission_processing_task
 *
 * @description correction in m220728_122043_add_abac_permission_processing_task
 */
class m220728_095905_add_abac_permission_processing_task extends AbacMigration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
    }
}
