<?php

use yii\db\Migration;

/**
 * Class m210329_142651_alter_column_or_lead_id
 */
class m210329_142651_alter_column_or_lead_id extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->dropForeignKey('FK-order-or_lead_id', '{{%order}}');

        $this->alterColumn('{{%order}}', 'or_lead_id', $this->integer());

        //$this->addForeignKey('FK-order-or_lead_id', '{{%order}}', ['or_lead_id'], '{{%leads}}', ['id'], 'SET NULL', 'CASCADE');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        //$this->dropForeignKey('FK-order-or_lead_id', '{{%order}}');

        $this->alterColumn('{{%order}}', 'or_lead_id', $this->integer()->notNull());

        $this->addForeignKey('FK-order-or_lead_id', '{{%order}}', ['or_lead_id'], '{{%leads}}', ['id'], 'CASCADE', 'CASCADE');
    }
}
