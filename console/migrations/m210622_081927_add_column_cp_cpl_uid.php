<?php

use yii\db\Migration;

/**
 * Class m210622_081927_add_column_cp_cpl_uid
 */
class m210622_081927_add_column_cp_cpl_uid extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%client_phone}}', 'cp_cpl_uid', $this->string(36));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%client_phone}}', 'cp_cpl_uid');
    }
}
