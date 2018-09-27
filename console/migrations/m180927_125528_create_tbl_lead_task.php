<?php

use yii\db\Migration;

/**
 * Class m180927_125528_create_tbl_lead_task
 */
class m180927_125528_create_tbl_lead_task extends Migration
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
        $this->dropTable('{{%lead_task}}');
        $this->dropTable('{{%task}}');
    }

}
