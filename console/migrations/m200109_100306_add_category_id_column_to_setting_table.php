<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%setting}}`.
 */
class m200109_100306_add_category_id_column_to_setting_table extends Migration
{
    private $_columnName = 's_category_id';

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%setting}}', $this->_columnName, $this->integer());
        $this->addForeignKey(
            'fk-setting-setting_category',
            '{{%setting}}',
            $this->_columnName,
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
        $this->dropColumn('{{%setting}}', $this->_columnName);
    }
}
