<?php

use yii\db\Migration;

/**
 * Class m220712_100500_add_new_column_into_client_data_table
 */
class m220712_100500_add_new_column_into_client_data_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%client_data}}', 'cd_field_value_ui', $this->string(500));
        \Yii::$app->db->getSchema()->refreshTableSchema('{{%client_data}}');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%client_data}}', 'cd_field_value_ui');
        \Yii::$app->db->getSchema()->refreshTableSchema('{{%client_data}}');
    }
}
