<?php

use src\helpers\app\DBHelper;
use yii\db\Migration;

/**
 * Class m220504_112900_add_column_create_type_id_to_quote
 */
class m220504_112900_add_column_create_type_id_to_quote extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        if (!DBHelper::isColumnExist('quotes', 'q_create_type_id')) {
            $this->addColumn('{{%quotes}}', 'q_create_type_id', $this->integer());
            Yii::$app->db->getSchema()->refreshTableSchema('{{%quotes}}');
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        if (DBHelper::isColumnExist('quotes', 'q_create_type_id')) {
            $this->dropColumn('{{%quotes}}', 'q_create_type_id');
            Yii::$app->db->getSchema()->refreshTableSchema('{{%quotes}}');
        }
    }
}
