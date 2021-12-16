<?php

use yii\db\Migration;

/**
 * Class m210910_074952_add_column_luc_created_user_id
 */
class m210910_074952_add_column_luc_created_user_id extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%lead_user_conversion}}', 'luc_created_user_id', $this->integer());
        $this->addForeignKey(
            'FK-lead_user_conversion-luc_created_user_id',
            '{{%lead_user_conversion}}',
            'luc_created_user_id',
            '{{%employees}}',
            'id',
            'SET NULL',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('FK-lead_user_conversion-luc_created_user_id', '{{%lead_user_conversion}}');
        $this->dropColumn('{{%lead_user_conversion}}', 'luc_created_user_id');
    }
}
