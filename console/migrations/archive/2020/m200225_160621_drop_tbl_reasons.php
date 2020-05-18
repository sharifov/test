<?php

use yii\db\Migration;

/**
 * Class m200225_160621_drop_tbl_reasons
 */
class m200225_160621_drop_tbl_reasons extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        try {
            $this->dropTable('{{%reasons}}');
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
