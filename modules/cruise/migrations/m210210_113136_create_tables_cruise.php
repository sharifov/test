<?php

namespace modules\cruise\migrations;

use yii\db\Migration;

/**
 * Class m210210_113136_create_tables_cruise
 */
class m210210_113136_create_tables_cruise extends Migration
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

        $this->createTable('{{%cruise}}', [
            'crs_id' => $this->primaryKey(),
            'crs_product_id' => $this->integer(),
            'crs_departure_date_from' => $this->date(),
            'crs_arrival_date_to' => $this->date(),
            'crs_destination_code' => $this->string(50),
            'crs_destination_label' => $this->string(100),
        ], $tableOptions);

        $this->addForeignKey('FK-cruise-crs_product_id', '{{%cruise}}', ['crs_product_id'], '{{%product}}', ['pr_id'], 'CASCADE', 'CASCADE');

        $this->createTable('{{%cruise_cabin}}', [
            'crc_id' => $this->primaryKey(),
            'crc_cruise_id' => $this->integer()->notNull(),
            'crc_name' => $this->string(200),
        ], $tableOptions);

        $this->addForeignKey('FK-cruise_cabin-crc_cruise_id', '{{%cruise_cabin}}', ['crc_cruise_id'], '{{%cruise}}', ['crs_id'], 'CASCADE', 'CASCADE');

        $this->createTable('{{%cruise_cabin_pax}}', [
            'crp_id' => $this->primaryKey(),
            'crp_cruise_cabin_id' => $this->integer()->notNull(),
            'crp_type_id' => $this->tinyInteger()->notNull(),
            'crp_age' => $this->tinyInteger(2),
            'crp_first_name' => $this->string(40),
            'crp_last_name' => $this->string(40),
            'crp_dob' => $this->date(),
        ], $tableOptions);

        $this->addForeignKey('FK-cruise_cabin_pax-crp_cruise_cabin_id', '{{%cruise_cabin_pax}}', ['crp_cruise_cabin_id'], '{{%cruise_cabin}}', ['crc_id'], 'CASCADE', 'CASCADE');
        $this->createIndex('IND-cruise_cabin_pax-crp_type_id', '{{%cruise_cabin_pax}}', ['crp_type_id']);

        $this->createTable('{{%cruise_quote}}', [
            'crq_id' => $this->primaryKey(),
            'crq_hash_key' => $this->string(50),
            'crq_product_quote_id' => $this->integer(),
            'crq_cruise_id' => $this->integer(),
            'crq_data_json' => $this->json(),
        ], $tableOptions);

        $this->addForeignKey('FK-cruise_quote-crq_product_quote_id', '{{%cruise_quote}}', ['crq_product_quote_id'], '{{%product_quote}}', ['pq_id'], 'CASCADE', 'CASCADE');
        $this->addForeignKey('FK-cruise_quote-crq_cruise_id', '{{%cruise_quote}}', ['crq_cruise_id'], '{{%cruise}}', ['crs_id'], 'CASCADE', 'CASCADE');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%cruise_quote}}');
        $this->dropTable('{{%cruise_cabin_pax}}');
        $this->dropTable('{{%cruise_cabin}}');
        $this->dropTable('{{%cruise}}');
    }
}
