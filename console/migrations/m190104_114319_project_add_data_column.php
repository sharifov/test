<?php

use yii\db\Migration;

/**
 * Class m190104_114319_project_add_data_column
 */
class m190104_114319_project_add_data_column extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%projects}}', 'custom_data', $this->text());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%projects}}', 'custom_data');
    }
}
