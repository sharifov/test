<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%quote_flight_program}}`.
 */
class m220830_064713_create_quote_flight_program_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $programs = [
            ['gfp_name' => 'Air Canada', 'gfp_airline_iata' => 'AC', 'gfp_ppm' => 0.0125, 'gfp_created_dt' => date('Y-m-d H:i:s')],
            ['gfp_name' => 'Air France - Flying blue', 'gfp_airline_iata' => 'AF', 'gfp_ppm' => 0.0135, 'gfp_created_dt' => date('Y-m-d H:i:s')],
            ['gfp_name' => 'Singapore Airline - Krisflyer', 'gfp_airline_iata' => 'SQ', 'gfp_ppm' => 0.014, 'gfp_created_dt' => date('Y-m-d H:i:s')],
            ['gfp_name' => 'Miles & More', 'gfp_airline_iata' => 'LH', 'gfp_ppm' => 0.02, 'gfp_created_dt' => date('Y-m-d H:i:s')],
            ['gfp_name' => 'Miles & Smiles', 'gfp_airline_iata' => 'TK', 'gfp_ppm' => 0.014, 'gfp_created_dt' => date('Y-m-d H:i:s')],
            ['gfp_name' => 'Cathay Pacific - Marco Polo', 'gfp_airline_iata' => 'CX', 'gfp_ppm' => 0.013, 'gfp_created_dt' => date('Y-m-d H:i:s')],
            ['gfp_name' => 'United MileagePlus', 'gfp_airline_iata' => 'UA', 'gfp_ppm' => 0.016, 'gfp_created_dt' => date('Y-m-d H:i:s')],
            ['gfp_name' => 'Executive Club - British Airways', 'gfp_airline_iata' => 'BA', 'gfp_ppm' => 0.0125, 'gfp_created_dt' => date('Y-m-d H:i:s')],
            ['gfp_name' => 'Qantas', 'gfp_airline_iata' => 'QF', 'gfp_ppm' => 0.012, 'gfp_created_dt' => date('Y-m-d H:i:s')],
            ['gfp_name' => 'Virgin Atlantic - Flying Club', 'gfp_airline_iata' => 'VS', 'gfp_ppm' => 0.012, 'gfp_created_dt' => date('Y-m-d H:i:s')]
        ];

        $this->createTable('{{%quote_flight_program}}', [
            'gfp_id' => $this->primaryKey(),
            'gfp_name' => $this->string()->notNull(),
            'gfp_airline_iata' => $this->string()->notNull(),
            'gfp_ppm' => $this->decimal(10, 4),
            'gfp_created_dt' => $this->dateTime(),
            'gfp_updated_dt' => $this->dateTime(),
            'gfp_updated_user_id' => $this->integer(),
        ]);

        $this->addForeignKey('FK-quote_flight_program-gfp_updated_user_id', '{{%quote_flight_program}}', 'gfp_updated_user_id', '{{%employees}}', 'id', 'SET NULL');

        $this->batchInsert('quote_flight_program', ['gfp_name', 'gfp_airline_iata', 'gfp_ppm', 'gfp_created_dt'], $programs);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%quote_flight_program}}');
    }
}
