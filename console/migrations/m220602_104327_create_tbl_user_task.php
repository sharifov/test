<?php

use yii\db\Migration;

/**
 * Class m220602_104327_create_tbl_user_task
 */
class m220602_104327_create_tbl_user_task extends Migration
{
    private string $tableUserTask = 'user_task';
    private string $tableShiftTask = 'shift_schedule_event_task';

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%' . $this->tableUserTask . '}}', [
            'ut_id' => $this->integer()->notNull()->unsigned(),
            'ut_user_id' => $this->integer()->notNull(),
            'ut_target_object' => $this->string(50),
            'ut_target_object_id' => $this->bigInteger()->notNull(),
            'ut_task_list_id' => $this->integer()->notNull(),
            'ut_start_dt' => $this->dateTime()->notNull(),
            'ut_end_dt' => $this->dateTime()->notNull(),
            'ut_priority' => $this->tinyInteger()->unsigned(),
            'ut_status_id' => $this->tinyInteger()->unsigned(),
            'ut_created_dt' => $this->dateTime(),
            'ut_year' => $this->smallInteger()->unsigned()->notNull(),
            'ut_month' => $this->tinyInteger()->unsigned()->notNull(),
        ], $tableOptions);

        $this->addPrimaryKey('PK-user_task', '{{%' . $this->tableUserTask . '}}', ['ut_id', 'ut_year', 'ut_month']);
        $this->alterColumn('{{%' . $this->tableUserTask . '}}', 'ut_id', $this->integer()->notNull()->unsigned() . ' AUTO_INCREMENT');

        $this->createIndex('IND-user_task-ut_user_id', '{{%' . $this->tableUserTask . '}}', 'ut_user_id');
        $this->createIndex('IND-user_task-ut_target_object', '{{%' . $this->tableUserTask . '}}', 'ut_target_object');
        $this->createIndex('IND-user_task-ut_target_object_id', '{{%' . $this->tableUserTask . '}}', 'ut_target_object_id');
        $this->createIndex('IND-user_task-ut_task_list_id', '{{%' . $this->tableUserTask . '}}', 'ut_task_list_id');
        $this->createIndex('IND-user_task-ut_start_dt', '{{%' . $this->tableUserTask . '}}', 'ut_start_dt');
        $this->createIndex('IND-user_task-ut_year', '{{%' . $this->tableUserTask . '}}', 'ut_year');
        $this->createIndex('IND-user_task-ut_month', '{{%' . $this->tableUserTask . '}}', 'ut_month');

        $partitions = \src\helpers\app\DBHelper::generateYearMonthPartition(
            $this->tableUserTask,
            'ut_year',
            'ut_month',
            //(new \DateTimeImmutable()), //
            (new \DateTimeImmutable())->modify('- 1 years'),
            1,// TODO:: FOR DEBUG:: must by change to 5
            false
        );
        Yii::$app->db->createCommand($partitions)->execute();

        $this->createTable('{{%' . $this->tableShiftTask . '}}', [
            'sset_event_id' => $this->integer()->notNull(),
            'sset_user_task_id' => $this->integer()->notNull()->unsigned(),
            'sset_created_dt' => $this->dateTime()
        ], $tableOptions);

        $this->addPrimaryKey('PK-shift_schedule_event_task', '{{%' . $this->tableShiftTask . '}}', ['sset_event_id', 'sset_user_task_id']);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%' . $this->tableShiftTask . '}}');
        $this->dropTable('{{%' . $this->tableUserTask . '}}');
    }
}
