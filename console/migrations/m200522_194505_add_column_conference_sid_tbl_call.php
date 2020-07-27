<?php

use yii\db\Migration;

/**
 * Class m200522_194505_add_column_conference_sid_tbl_call
 */
class m200522_194505_add_column_conference_sid_tbl_call extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%call}}', 'c_conference_sid', $this->string(34));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%call}}', 'c_conference_sid');
    }
}
