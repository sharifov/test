<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%setting_category}}`.
 */
class m200109_095057_create_setting_category_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%setting_category}}', [
            'sc_id' => $this->primaryKey(),
            'sc_name' => $this->string(),
            'sc_enabled' => $this->boolean()->defaultValue(true),
            'sc_created_dt' => $this->dateTime()->defaultExpression('NOW()'),
            'sc_updated_dt' => $this->dateTime()->defaultExpression('NOW()'),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%setting_category}}');
    }
}
