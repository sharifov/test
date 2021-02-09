<?php

use yii\db\Migration;

/**
 * Class m201210_142448_add_column_cl_excluded_tbl_clients
 */
class m201210_142448_add_column_cl_excluded_tbl_clients extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%clients}}', 'cl_ppn', $this->string(10));
        $this->addColumn('{{%clients}}', 'cl_excluded', $this->boolean()->defaultValue(false));
        $this->createIndex('IND-clients-cl_excluded', '{{%clients}}', ['cl_excluded']);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropIndex('IND-clients-cl_excluded', '{{%clients}}');
        $this->dropColumn('{{%clients}}', 'cl_excluded');
        $this->dropColumn('{{%clients}}', 'cl_ppn');
    }
}
