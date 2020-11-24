<?php

use sales\model\clientChatRequest\entity\ClientChatRequest;
use yii\db\Migration;

/**
 * Class m201020_085832_migrate_tbl_client_chat_request_to_postgresql
 */
class m201020_085832_migrate_tbl_client_chat_request_to_postgresql extends Migration
{
    public function init()
    {
        $this->db = 'db_postgres';
        parent::init();
    }

    /**
     * {@inheritdoc}
     * @throws \yii\db\Exception
     */
    public function safeUp()
    {
        $tableOptions = 'PARTITION BY RANGE (ccr_created_dt)';
        $this->createTable('{{%client_chat_request}}', [
            'ccr_id' => $this->integer(),
            'ccr_event' => $this->tinyInteger(2),
            'ccr_rid' => $this->string(150),
            'ccr_json_data' => $this->text(),
            'ccr_created_dt' => $this->dateTime(),
            'ccr_visitor_id' => $this->string(100),
            'ccr_job_id' => $this->integer(),
        ], $tableOptions);

        $this->addPrimaryKey('PK-client_chat_request', '{{%client_chat_request}}', ['ccr_id', 'ccr_created_dt']);
        $this->createIndex('IND-ccr_event', '{{%client_chat_request}}', ['ccr_event']);
        $this->createIndex('IND-ccr_rid', '{{%client_chat_request}}', ['ccr_rid']);

        $now = date_create("now");

        $datesPartition = ClientChatRequest::partitionDatesFrom($now);
        ClientChatRequest::createMonthlyPartition($datesPartition[0], $datesPartition[1]);

        $dbMysql = Yii::$app->db;

        $dates = $dbMysql->createCommand('select DISTINCT DATE_FORMAT(ccr_created_dt, "%Y-%m-01 00:00:00") as `ccr_created_dt` from client_chat_request')->queryColumn();

        foreach ($dates as $date) {
            $partitionsDate = ClientChatRequest::partitionDatesFrom((new DateTime($date)));
            if (is_array($partitionsDate) && $partitionsDate[0]->format('Y-m') !== $datesPartition[0]->format('Y-m')) {
                ClientChatRequest::createMonthlyPartition($partitionsDate[0], $partitionsDate[1]);
            }
        }

        $clientChatRequests = $dbMysql->createCommand('select * from client_chat_request')->queryAll();

        $values = '';
        foreach ($clientChatRequests as $clientChatRequest) {
            $id = $clientChatRequest['ccr_id'] ?: 'DEFAULT';
            $event = $clientChatRequest['ccr_event'] ?: 'DEFAULT';
            $rid = $clientChatRequest['ccr_rid'] ? "'{$clientChatRequest['ccr_rid']}'" : 'DEFAULT';
            $jsonData = $clientChatRequest['ccr_json_data'] ? "'" . str_replace("'", "''", $clientChatRequest['ccr_json_data']) . "'" : 'DEFAULT';
            $createdDt = $clientChatRequest['ccr_created_dt'] ? "'{$clientChatRequest['ccr_created_dt']}'" : 'DEFAULT';
            $visitorId = $clientChatRequest['ccr_visitor_id'] ? "'{$clientChatRequest['ccr_visitor_id']}'" : 'DEFAULT';
            $jobId = $clientChatRequest['ccr_job_id'] ?: 'DEFAULT';
            $values .=  "(" . "{$id}," . "{$event}," . "{$rid}," . "{$jsonData}," . "{$createdDt}," . "{$visitorId}," . "{$jobId}" . "),";
        }

        $dbPostgres = Yii::$app->get('db_postgres');
        $restartWith = 1;
        if ($values) {
            /** @var $dbPostgres \yii\db\Connection */
            $dbPostgres->createCommand('insert into client_chat_request (ccr_id, ccr_event, ccr_rid, ccr_json_data, ccr_created_dt, ccr_visitor_id, ccr_job_id) VALUES ' . rtrim($values, ','))->execute();
            $restartWith = $clientChatRequests[count($clientChatRequests) - 1]['ccr_id'] + 1;
        }
        $dbPostgres->createCommand('alter table client_chat_request alter column ccr_id add generated always as identity (INCREMENT BY ' . $restartWith . ')')->execute();
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%client_chat_request}}');
    }
}
