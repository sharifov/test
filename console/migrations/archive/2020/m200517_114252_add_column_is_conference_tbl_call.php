<?php

use yii\db\Migration;

/**
 * Class m200517_114252_add_column_is_conference_tbl_call
 */
class m200517_114252_add_column_is_conference_tbl_call extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%call}}', 'c_is_conference', $this->boolean()->defaultValue(false));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%call}}', 'c_is_conference');
    }
}
