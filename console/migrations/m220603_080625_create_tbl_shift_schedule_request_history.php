<?php

use src\helpers\app\DBHelper;
use yii\db\Migration;

/**
 * Class m220603_080625_create_tbl_shift_schedule_request_history
 */
class m220603_080625_create_tbl_shift_schedule_request_history extends Migration
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

        DBHelper::dropTableIfExists('{{%shift_schedule_request_history}}');

        $this->createTable('{{%shift_schedule_request_history}}', [
            'ssrh_id' => $this->primaryKey(),
            'ssrh_ssr_id' => $this->integer(),
            'ssrh_old_attr' => $this->json(),
            'ssrh_new_attr' => $this->json(),
            'ssrh_formatted_attr' => $this->json(),
            'ssrh_created_dt' => $this->dateTime(),
            'ssrh_updated_dt' => $this->dateTime(),
            'ssrh_created_user_id' => $this->integer(),
            'ssrh_updated_user_id' => $this->integer(),
        ], $tableOptions);

        $this->addForeignKey(
            'FK-shift_schedule_request_history-ssrh_ssr_id',
            '{{%shift_schedule_request_history}}',
            'ssrh_ssr_id',
            '{{%shift_schedule_request}}',
            'ssr_id',
            'CASCADE',
            'CASCADE'
        );

        $this->addForeignKey(
            'FK-shift_schedule_request_history-ssrh_created_user_id',
            '{{%shift_schedule_request_history}}',
            'ssrh_created_user_id',
            '{{%employees}}',
            'id',
            'CASCADE',
            'CASCADE'
        );

        $this->addForeignKey(
            'FK-shift_schedule_request_history-ssrh_updated_user_id',
            '{{%shift_schedule_request_history}}',
            'ssrh_updated_user_id',
            '{{%employees}}',
            'id',
            'SET NULL',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%shift_schedule_request_history}}');
    }
}
