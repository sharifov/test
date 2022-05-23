<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%user_shift_schedule_log}}`.
 */
class m220520_103734_create_user_shift_schedule_log_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%user_shift_schedule_log}}', [
            'ussl_id' => $this->integer(),
            'ussl_uss_id' => $this->integer()->notNull(),
            'ussl_old_attr' => $this->json(),
            'ussl_new_attr' => $this->json(),
            'ussl_formatted_attr' => $this->json(),
            'ussl_created_user_id' => $this->integer(),
            'ussl_created_dt' => $this->dateTime(),
            'ussl_month_start' => $this->tinyInteger(),
            'ussl_year_start' => $this->smallInteger()
        ]);

        $this->addPrimaryKey('PK-user_shift_schedule_log', '{{%user_shift_schedule_log}}', ['ussl_id', 'ussl_month_start', 'ussl_year_start']);
        $this->createIndex('IND-user_shift_schedule_log-ussl_uss_id', '{{%user_shift_schedule_log}}', 'ussl_uss_id');
        $this->createIndex('IND-user_shift_schedule_log-ussl_created_user_id', '{{%user_shift_schedule_log}}', 'ussl_created_user_id');

        Yii::$app->db->createCommand('ALTER TABLE `user_shift_schedule_log` PARTITION BY RANGE (`ussl_year_start`)
SUBPARTITION BY LINEAR HASH (`ussl_month_start`)
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
        \Yii::$app->db->getSchema()->refreshTableSchema('{{%user_shift_schedule_log}}');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%user_shift_schedule_log}}');
        \Yii::$app->db->getSchema()->refreshTableSchema('{{%user_shift_schedule_log}}');
    }
}
