<?php

namespace modules\flight\migrations;

use yii\db\Migration;

/**
 * Class m210804_095112_create_flight_request_log_tbl
 */
class m210804_095112_create_flight_request_log_tbl extends Migration
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

        $this->createTable('{{%flight_request_log}}', [
            'flr_id' => $this->primaryKey(),
            'flr_fr_id' => $this->integer(),
            'flr_status_id_old' => $this->tinyInteger(),
            'flr_status_id_new' => $this->tinyInteger(),
            'flr_description' => $this->string(500),
            'flr_created_dt' => $this->dateTime(),
            'flr_updated_dt' => $this->dateTime()
        ], $tableOptions);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%flight_request_log}}');
    }
}
