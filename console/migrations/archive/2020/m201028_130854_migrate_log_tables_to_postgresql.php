<?php

use common\models\ApiLog;
use yii\db\Migration;

/**
 * Class m201028_130854_migrate_log_tables_to_postgresql
 */
class m201028_130854_migrate_log_tables_to_postgresql extends Migration
{
    public function init()
    {
        $this->db = 'db_postgres';
        parent::init();
    }

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $db = $this->db;

        $this->createTable('{{%log}}', [
            'id' => $this->primaryKey(),
            'level' => $this->integer(),
            'category' => $this->string(),
            'log_time' => $this->double(),
            'prefix' => $this->text(),
            'message' => $this->text(),
        ]);
        $this->createIndex('IDX_category', '{{%log}}', 'category');
        $this->createIndex('IDX_level', '{{%log}}', 'level');
        $this->createIndex('IDX_log_time', '{{%log}}', 'log_time');

        $now = date_create("now");

        $tableOptions = 'PARTITION BY RANGE (al_created_dt)';
        $this->createTable('{{%api_log}}', [
            'al_id' => $this->integer(),
            'al_request_data' => $this->text(),
            'al_request_dt' => $this->dateTime(),
            'al_response_data' => $this->text(),
            'al_response_dt' => $this->dateTime(),
            'al_ip_address' => $this->string(40),
            'al_user_id' => $this->integer(),
            'al_action' => $this->string(),
            'al_execution_time' => $this->decimal(6, 3),
            'al_memory_usage' => $this->integer(),
            'al_db_execution_time' => $this->decimal(6, 3),
            'al_db_query_count' => $this->integer(),
            'al_created_dt' => $this->dateTime()
        ], $tableOptions);

        $this->addPrimaryKey('PK-al_id-al_created_dt', '{{%api_log}}', ['al_id', 'al_created_dt']);
        $this->createIndex('IDX_api_log_index', '{{%api_log}}', ['al_user_id', 'al_request_dt']);
        $this->createIndex('IDX_api_log_al_action', '{{%api_log}}', 'al_action');

        $db->createCommand('alter table api_log alter column al_id add generated always as identity (INCREMENT BY 1)')->execute();

        $datesPartition = ApiLog::partitionDatesFrom($now);
        ApiLog::createMonthlyPartition($datesPartition[0], $datesPartition[1]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%log}}');
        $this->dropTable('{{%api_log}}');
    }
}
