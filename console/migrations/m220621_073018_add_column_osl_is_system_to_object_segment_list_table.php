<?php

use yii\db\Migration;

/**
 * Class m220621_073018_add_column_osl_is_system_to_object_segment_list_table
 */
class m220621_073018_add_column_osl_is_system_to_object_segment_list_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn(
            '{{%object_segment_list}}',
            'osl_is_system',
            $this->boolean()->defaultValue(false)
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%object_segment_list}}', 'osl_is_system');
    }
}
