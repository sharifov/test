<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%setting}}`.
 */
class m200109_100306_add_category_id_column_to_setting_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%setting}}', 'category_id', $this->integer());
        $this->addForeignKey(
            'fk-setting-setting_category',
            '{{%setting}}',
            'category_id',
            '{{%setting_category}}',
            'sc_id',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('fk-setting-setting_category', '{{%setting}}');
        $this->dropColumn('{{%setting}}', 'category_id');
    }
}
