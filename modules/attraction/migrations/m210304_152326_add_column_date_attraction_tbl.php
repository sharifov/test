<?php

namespace modules\attraction\migrations;

use yii\db\Migration;

/**
 * Class m210304_152326_add_column_date_attraction_tbl
 */
class m210304_152326_add_column_date_attraction_tbl extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%attraction_quote}}', 'atnq_date', $this->date());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%attraction_quote}}', 'atnq_date');
    }
}
