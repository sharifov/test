<?php

use yii\db\Migration;

/**
 * Class m201119_063726_fix_column_name_client_ca_id
 */
class m201119_063726_fix_column_name_client_ca_id extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->dropForeignKey('FK-clients-c_ca_id', '{{%clients}}');
        $this->dropColumn('{{%clients}}', 'c_ca_id');

        $this->addColumn('{{%clients}}', 'cl_ca_id', $this->integer());
        $this->addForeignKey(
            'FK-clients-cl_ca_id',
            '{{%clients}}',
            'cl_ca_id',
            '{{%client_account}}',
            'ca_id',
            'SET NULL',
            'CASCADE'
        );

        $this->alterColumn('{{%client_account}}', 'ca_first_name', $this->string(100)->null()->defaultValue(null));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('FK-clients-cl_ca_id', '{{%clients}}');
        $this->dropColumn('{{%clients}}', 'cl_ca_id');

        $this->addColumn('{{%clients}}', 'c_ca_id', $this->integer());
        $this->addForeignKey(
            'FK-clients-c_ca_id',
            '{{%clients}}',
            'c_ca_id',
            '{{%client_account}}',
            'ca_id',
            'SET NULL',
            'CASCADE'
        );

        $this->alterColumn('{{%client_account}}', 'ca_first_name', $this->string(100)->notNull());
    }
}
