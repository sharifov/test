<?php

namespace modules\flight\migrations;

use yii\db\Migration;

/**
 * Class m210315_123637_alter_tbl_flight_quote_option_add_columns
 */
class m210315_123637_alter_tbl_flight_quote_option_add_columns extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%flight_quote_option}}', 'fqo_usd_total_price', $this->decimal(10, 2)->after('fqo_total_price'));
        $this->addColumn('{{%flight_quote_option}}', 'fqo_usd_base_price', $this->decimal(10, 2)->after('fqo_base_price'));
        $this->addColumn('{{%flight_quote_option}}', 'fqo_usd_markup_amount', $this->decimal(10, 2)->after('fqo_markup_amount'));
        $this->addColumn('{{%flight_quote_option}}', 'fqo_currency', $this->string(5));

        $this->addForeignKey('FK-flight_quote_option-fqo_currency', '{{%flight_quote_option}}', 'fqo_currency', '{{%currency}}', 'cur_code', 'CASCADE', 'CASCADE');

        $this->dropColumn('{{%flight_quote_option}}', 'fqo_client_total');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('FK-flight_quote_option-fqo_currency', '{{%flight_quote_option}}');
        $this->dropColumn('{{%flight_quote_option}}', 'fqo_usd_total_price');
        $this->dropColumn('{{%flight_quote_option}}', 'fqo_usd_base_price');
        $this->dropColumn('{{%flight_quote_option}}', 'fqo_usd_markup_amount');
        $this->dropColumn('{{%flight_quote_option}}', 'fqo_currency');

        $this->addColumn('{{%flight_quote_option}}', 'fqo_client_total', $this->decimal(10, 2));
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210315_123637_alter_tbl_flight_quote_option_add_columns cannot be reverted.\n";

        return false;
    }
    */
}
