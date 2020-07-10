<?php

use yii\db\Migration;

/**
 * Class m200710_050552_add_column_tracking_id_to_project
 */
class m200710_050552_add_column_tracking_id_to_project extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%projects}}', 'ga_tracking_id', $this->string(255));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%projects}}', 'ga_tracking_id');
    }
}
