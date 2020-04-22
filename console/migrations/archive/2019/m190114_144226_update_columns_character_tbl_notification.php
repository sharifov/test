<?php

use yii\db\Migration;

/**
 * Class m190114_144226_update_columns_character_tbl_notification
 */
class m190114_144226_update_columns_character_tbl_notification extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->alterColumn('{{%notifications}}', 'n_title', $this->string(100)->append('CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci'));
        $this->alterColumn('{{%notifications}}', 'n_message', $this->text()->append('CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {

    }
}
