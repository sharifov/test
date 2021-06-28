<?php

use yii\db\Migration;

/**
 * Class m210616_103303_alter_tbl_quote_segment_add_new_column
 */
class m210616_103303_alter_tbl_quote_segment_add_new_column extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%quote_segment}}', 'qs_cabin_basic', $this->boolean()->defaultValue(0)->after('qs_cabin'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%quote_segment}}', 'qs_cabin_basic');
    }
}
