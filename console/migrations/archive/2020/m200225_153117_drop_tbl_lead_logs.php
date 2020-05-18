<?php

use yii\db\Migration;

/**
 * Class m200225_153117_drop_tbl_lead_logs
 */
class m200225_153117_drop_tbl_lead_logs extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        try {
            $this->dropTable('{{%lead_logs}}');
        } catch (Throwable $e) {
            echo $e->getMessage();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        return true;
    }
}
