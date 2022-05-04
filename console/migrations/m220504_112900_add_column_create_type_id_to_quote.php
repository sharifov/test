<?php

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
        $this->addColumn('{{%quotes}}', 'create_type_id', $this->integer());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220504_112900_add_column_create_type_id_to_quote cannot be reverted.\n";

        return false;
    }
}
