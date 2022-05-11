<?php

use src\helpers\app\DBHelper;
use yii\db\Migration;

/**
 * Handles the creation of table `{{%partition_for_user_shift_schedule}}`.
 */
class m220506_114743_create_partition_for_user_shift_schedule_table extends Migration
{
    private string $tableName = '{{%user_shift_schedule}}';

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        if (DBHelper::isIndexExist('user_shift_schedule', 'FK-user_shift_schedule-uss_shift_id')) {
            $this->dropForeignKey('FK-user_shift_schedule-uss_shift_id', $this->tableName);
        }
        if (DBHelper::isIndexExist('user_shift_schedule', 'FK-user_shift_schedule-uss_ssr_id')) {
            $this->dropForeignKey('FK-user_shift_schedule-uss_ssr_id', $this->tableName);
        }
        if (DBHelper::isIndexExist('user_shift_schedule', 'FK-user_shift_schedule-uss_sst_id')) {
            $this->dropForeignKey('FK-user_shift_schedule-uss_sst_id', $this->tableName);
        }
        if (DBHelper::isIndexExist('user_shift_schedule', 'FK-user_shift_schedule-uss_user_id')) {
            $this->dropForeignKey('FK-user_shift_schedule-uss_user_id', $this->tableName);
        }

        Yii::$app->db->createCommand('ALTER TABLE `user_shift_schedule` drop primary key, add primary key(uss_id, uss_year_start, uss_month_start)')->execute();

        Yii::$app->db->createCommand('ALTER TABLE `user_shift_schedule` PARTITION BY RANGE (`uss_year_start`)
SUBPARTITION BY LINEAR HASH (`uss_month_start`)
SUBPARTITIONS 12
(
PARTITION y22 VALUES LESS THAN (2022)ENGINE = InnoDB,
PARTITION y23 VALUES LESS THAN (2023)ENGINE = InnoDB,
PARTITION y24 VALUES LESS THAN (2024)ENGINE = InnoDB,
PARTITION y25 VALUES LESS THAN (2025)ENGINE = InnoDB,
PARTITION y26 VALUES LESS THAN (2026)ENGINE = InnoDB,
PARTITION y27 VALUES LESS THAN (2027)ENGINE = InnoDB,
PARTITION y28 VALUES LESS THAN (2028)ENGINE = InnoDB,
PARTITION y29 VALUES LESS THAN (2029)ENGINE = InnoDB,
PARTITION y30 VALUES LESS THAN (2030)ENGINE = InnoDB,
 PARTITION y VALUES LESS THAN MAXVALUE
 );')->execute();
        \Yii::$app->db->getSchema()->refreshTableSchema($this->tableName);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        Yii::$app->db->createCommand('ALTER TABLE `user_shift_schedule` remove partitioning')->execute();
        \Yii::$app->db->getSchema()->refreshTableSchema($this->tableName);
    }
}
