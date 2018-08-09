<?php

use yii\db\Migration;

/**
 * Handles the creation of table `quotes`.
 */
class m180808_113909_create_quotes_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }

        $this->createTable('quotes', [
            'id' => $this->primaryKey(),
            'uid' => $this->string()->notNull(),
            'lead_id' => $this->integer(),
            'employee_id' => $this->integer(),
            'record_locator' => $this->string(),
            'pcc' => $this->string(),
            'cabin' => $this->string(),
            'gds' => $this->string(),
            'trip_type' => $this->string(),
            'main_airline_code' => $this->string(),
            'reservation_dump' => $this->text()->notNull(),
            'status' => $this->integer(),
            'check_payment' => $this->boolean(),
            'fare_type' => $this->string(),
            'created' => $this->dateTime()->defaultExpression('NOW()'),
            'updated' => $this->dateTime()->notNull()
        ], $tableOptions);

        $this->addForeignKey('fk-quotes-leads', 'quotes', 'lead_id', 'leads', 'id');
        $this->addForeignKey('fk-quotes-employees', 'quotes', 'employee_id', 'employees', 'id');

        $this->createTable('quote_price', [
            'id' => $this->primaryKey(),
            'quote_id' => $this->integer(),
            'passenger_type' => $this->string(),
            'selling' => $this->float(2)->defaultValue(0),
            'net' => $this->float(2)->defaultValue(0),
            'fare' => $this->float(2)->defaultValue(0),
            'taxes' => $this->float(2)->defaultValue(0),
            'mark_up' => $this->float(2)->defaultValue(0),
            'extra_mark_up' => $this->float(2)->defaultValue(0),
            'created' => $this->dateTime()->defaultExpression('NOW()'),
            'updated' => $this->dateTime()->notNull()
        ], $tableOptions);

        $this->addForeignKey('fk-quote_price-quotes', 'quote_price', 'quote_id', 'quotes', 'id');
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropForeignKey('fk-quote_price-quotes', 'quote_price');
        $this->dropTable('quote_price');

        $this->dropForeignKey('fk-quotes-leads', 'quotes');
        $this->dropForeignKey('fk-quotes-employees', 'quotes');
        $this->dropTable('quotes');
    }
}
