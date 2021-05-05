<?php

namespace modules\flight\migrations;

use yii\db\Migration;

/**
 * Class m210421_133324_alter_column_fqs_marriage_group
 */
class m210421_133324_alter_column_fqs_marriage_group extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->alterColumn('{{%flight_quote_segment}}', 'fqs_marriage_group', $this->string(3));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210421_133324_alter_column_fqs_marriage_group cannot be reverted.\n";

        return false;
    }
}
