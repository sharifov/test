<?php

namespace modules\rentCar\migrations;

use yii\db\Migration;

/**
 * Class m210210_100318_initialization
 */
class m210210_100318_initialization extends Migration
{
    /**
     * {@inheritdoc}
     * @throws \yii\base\Exception
     */
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
        }

        $this->insert('{{%product_type}}', [
            'pt_id'             => 3,
            'pt_key'            => 'rent_car',
            'pt_name'           => 'Rent Car',
            'pt_enabled'        => false,
            'pt_service_fee_percent' => 3.5,
            'pt_created_dt'     => date('Y-m-d H:i:s'),
            'pt_updated_dt'     => date('Y-m-d H:i:s'),
        ]);

        $this->createTable('{{%rent_car}}', [
            'prc_id' => $this->primaryKey(),
            'prc_product_id' => $this->integer()->notNull(),
            'prc_pick_up_code' => $this->string(10),
            'prc_drop_off_code' => $this->string(10),
            'prc_request_hash_key' => $this->string(32),
            'prc_pick_up_date' => $this->date(),
            'prc_drop_off_date' => $this->date(),
            'prc_pick_up_time' => $this->time(),
            'prc_drop_off_time' => $this->time(),
            'prc_created_dt' => $this->dateTime(),
            'prc_updated_dt' => $this->dateTime(),
            'prc_created_user_id' => $this->integer(),
            'prc_updated_user_id' => $this->integer(),
        ], $tableOptions);

        $this->addForeignKey(
            'FK-rent_car-prc_product_id',
            '{{%rent_car}}',
            ['prc_product_id'],
            '{{%product}}',
            ['pr_id'],
            'CASCADE',
            'CASCADE'
        );

        $this->createTable('{{%rent_car_quote}}', [
            'rcq_id' => $this->primaryKey(),
            'rcq_rent_car_id' => $this->integer()->notNull(),
            'rcq_product_quote_id' => $this->integer()->notNull(),
            'rcq_hash_key' => $this->string(32)->unique(),
            'rcq_json_response' => $this->json(),
            'rcq_model_name' => $this->string(255)->notNull(),
            'rcq_category' => $this->string(255),
            'rcq_image_url' => $this->string(500),
            'rcq_vendor_name' => $this->string(255),
            'rcq_vendor_logo_url' => $this->string(500),
            'rcq_transmission' => $this->string(255),
            'rcq_seats' => $this->integer(),
            'rcq_doors' => $this->string(50),
            'rcq_options' => $this->json(),
            'rcq_days' => $this->integer()->defaultValue(1),
            'rcq_price_per_day' => $this->decimal(10, 2)->notNull(),
            'rcq_currency' => $this->string(3)->notNull()->defaultValue('USD'),
            'rcq_advantages' => $this->json(),
            'rcq_pick_up_location' => $this->string(255),
            'rcq_drop_of_location' => $this->string(255),
            'rcq_offer_token' =>  $this->string(500),
            'rcq_request_hash_key' => $this->string(32),
            'rcq_created_dt' => $this->dateTime(),
            'rcq_updated_dt' => $this->dateTime(),
            'rcq_created_user_id' => $this->integer(),
            'rcq_updated_user_id' => $this->integer(),
        ], $tableOptions);

        $this->addForeignKey(
            'FK-rent_car_quote-rcq_rent_car_id',
            '{{%rent_car_quote}}',
            ['rcq_rent_car_id'],
            '
            {{%rent_car}}',
            ['prc_id'],
            'CASCADE',
            'CASCADE'
        );
        $this->addForeignKey(
            'FK-rent_car_quote-rcq_product_quote_id',
            '{{%rent_car_quote}}',
            ['rcq_product_quote_id'],
            '{{%product_quote}}',
            ['pq_id'],
            'CASCADE',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%rent_car_quote}}');
        $this->dropTable('{{%rent_car}}');

        $this->delete('{{%product_type}}', ['IN', 'pt_key', ['rent_car']]);
    }
}
