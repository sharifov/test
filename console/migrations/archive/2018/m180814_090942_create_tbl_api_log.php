<?php

use yii\db\Migration;

/**
 * Class m180814_090942_create_tbl_api_log
 */
class m180814_090942_create_tbl_api_log extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%api_log}}', [
            'al_id'                     => $this->primaryKey(),
            'al_request_data'           => $this->text()->notNull(),
            'al_request_dt'             => $this->dateTime()->notNull(),
            'al_response_data'          => $this->text(),
            'al_response_dt'            => $this->dateTime(),
            'al_ip_address'             => $this->string(40),
            'al_user_id'                => $this->integer(),
            'al_action'                 => $this->string(255),
        ]);

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%api_log}}');
    }

}
