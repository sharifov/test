<?php

use yii\db\Migration;
use src\model\dbDataSensitive\dictionary\DbDataSensitiveDictionary;
use common\models\DbDataSensitive;

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
            DbDataSensitive::tableName(),
            'db_is_system',
            $this->boolean()->defaultValue(false)->notNull()
        );

        $this->update(DbDataSensitive::tableName(), [
            'db_is_system' => 1,
        ], [
            'dda_key' => DbDataSensitiveDictionary::KEY_VIEW,
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn(DbDataSensitive::tableName(), 'db_is_system');
    }
}
