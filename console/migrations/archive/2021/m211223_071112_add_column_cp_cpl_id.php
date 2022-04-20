<?php

use yii\db\Migration;

/**
 * Class m211223_071112_add_column_cp_cpl_id
 */
class m211223_071112_add_column_cp_cpl_id extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%client_phone}}', 'cp_cpl_id', $this->integer());

        $this->addForeignKey(
            'FK-client_phone-cp_cpl_id',
            '{{%client_phone}}',
            'cp_cpl_id',
            '{{%contact_phone_list}}',
            'cpl_id',
            'SET NULL',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('FK-client_phone-cp_cpl_id', '{{%client_phone}}');
        $this->dropColumn('{{%client_phone}}', 'cp_cpl_id');
    }
}
