<?php

namespace modules\order\migrations;

use yii\db\Migration;

/**
 * Class m210326_075313_create_tbl_order_request
 */
class m210326_075313_create_tbl_order_request extends Migration
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

        $this->createTable('{{%order_request}}', [
            'orr_id' => $this->primaryKey(),
            'orr_request_data_json' => $this->json(),
            'orr_response_data_json' => $this->json(),
            'orr_source_type_id' => $this->tinyInteger(1),
            'orr_response_type_id' => $this->tinyInteger(1),
            'orr_created_dt' => $this->dateTime(),
        ], $tableOptions);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%order_request}}');
    }
}
