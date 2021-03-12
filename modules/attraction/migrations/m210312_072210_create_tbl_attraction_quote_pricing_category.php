<?php

namespace modules\attraction\migrations;

use yii\db\Migration;

/**
 * Class m210312_072210_create_tbl_attraction_quote_pricing_category
 */
class m210312_072210_create_tbl_attraction_quote_pricing_category extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%attraction_quote_options}}', [
            'atqo_id' => $this->primaryKey(),
            'atqo_attraction_quote_id' => $this->integer()->notNull(),
            'atqo_answered_value' => $this->string(40),
            'atqo_label' => $this->string(40),
            'atqo_is_answered' => $this->tinyInteger()->defaultValue(0),
            'atqo_answer_formatted_text' => $this->string(255),
        ], $tableOptions);

        $this->addForeignKey('FK-attraction_quote_options_atqo_attraction_quote_id', '{{%attraction_quote_options}}', ['atqo_attraction_quote_id'], '{{%attraction_quote}}', ['atnq_id'], 'CASCADE', 'CASCADE');

        $this->createTable('{{%attraction_quote_pricing_category}}', [
            'atqpc_id' => $this->primaryKey(),
            'atqpc_attraction_quote_id' => $this->integer()->notNull(),
            'atqpc_category_id' => $this->string(40),
            'atqpc_label' => $this->string(40),
            'atqpc_min_age' => $this->integer(),
            'atqpc_max_age' => $this->integer(),
            'atqpc_min_participants' => $this->integer(),
            'atqpc_max_participants' => $this->integer(),
            'atqpc_quantity' => $this->integer(),
            'atqpc_price' => $this->decimal(10, 2)->defaultValue(0),
            'atqpc_currency' => $this->string(3)
        ], $tableOptions);

        $this->addForeignKey('FK-attraction_quote_pricing_category_atqpc_attraction_quote_id', '{{%attraction_quote_pricing_category}}', ['atqpc_attraction_quote_id'], '{{%attraction_quote}}', ['atnq_id'], 'CASCADE', 'CASCADE');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%attraction_quote_pricing_category}}');
        $this->dropTable('{{%attraction_quote_options}}');
    }
}
