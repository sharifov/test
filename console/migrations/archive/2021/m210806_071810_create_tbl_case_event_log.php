<?php

use yii\db\Migration;

/**
 * Class m210806_071810_create_tbl_case_event_log
 */
class m210806_071810_create_tbl_case_event_log extends Migration
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

        $this->createTable('{{%case_event_log}}', [
            'cel_id' => $this->primaryKey(),
            'cel_case_id' => $this->integer(),
            'cel_description' => $this->string(255),
            'cel_data_json' => $this->json(),
            'cel_created_dt' => $this->dateTime(),
        ], $tableOptions);

        $this->addForeignKey(
            'FK-case_event_log-cel_case_id',
            '{{%case_event_log}}',
            ['cel_case_id'],
            '{{%cases}}',
            ['cs_id'],
            'CASCADE',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%case_event_log}}');
    }
}
