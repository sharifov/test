<?php

namespace modules\flight\migrations;

use yii\db\Migration;

/**
 * Class m210616_121933_alter_tbl_flight_quote_segment_add_new_column
 */
class m210616_121933_alter_tbl_flight_quote_segment_add_new_column extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%flight_quote_segment}}', 'fqs_cabin_class_basic', $this->boolean()->defaultValue(0)->after('fqs_cabin_class'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%flight_quote_segment}}', 'fqs_cabin_class_basic');
    }
}
