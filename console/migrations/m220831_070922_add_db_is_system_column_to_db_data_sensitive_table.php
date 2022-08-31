<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%db_data_sensitive}}`.
 */
class m220831_070922_add_db_is_system_column_to_db_data_sensitive_table extends Migration
{
    const TABLE_NAME = '{{%db_data_sensitive}}';

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn(
            static::TABLE_NAME,
            'db_is_system',
            $this->boolean()->defaultValue(false)->notNull()
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn(static::TABLE_NAME, 'db_is_system');
    }
}
