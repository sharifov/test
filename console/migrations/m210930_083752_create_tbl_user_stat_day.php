<?php

use yii\db\Migration;

/**
 * Class m210930_083752_create_tbl_user_stat_day
 */
class m210930_083752_create_tbl_user_stat_day extends Migration
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
        $this->createTable('{{%user_stat_day}}', [
            'usd_id' => $this->integer()->notNull()->unsigned(),
            'usd_key' => $this->tinyInteger()->unsigned()->notNull(),
            'usd_value' => $this->decimal(8, 2),
            'usd_user_id' => $this->integer()->notNull(),
            'usd_day' => $this->tinyInteger(2),
            'usd_month' => $this->tinyInteger(2)->unsigned()->notNull(),
            'usd_year' => $this->smallInteger()->unsigned()->notNull(),
            'usd_created_dt' => $this->dateTime()
        ], $tableOptions);

        $this->addPrimaryKey('PK-user_stat_day', '{{%user_stat_day}}', ['usd_id', 'usd_month', 'usd_year']);
        $this->alterColumn('{{%user_stat_day}}', 'usd_id', $this->integer()->notNull()->unsigned() . ' AUTO_INCREMENT');

        $this->createIndex('IND-user_stat_day-usd_key', '{{%user_stat_day}}', 'usd_key');
        $this->createIndex('IND-user_stat_day-usd_day', '{{%user_stat_day}}', 'usd_day');
        $this->createIndex('IND-user_stat_day-usd_month', '{{%user_stat_day}}', 'usd_month');
        $this->createIndex('IND-user_stat_day-usd_year', '{{%user_stat_day}}', 'usd_year');

        Yii::$app->db->createCommand('ALTER TABLE `user_stat_day` PARTITION BY RANGE (`usd_year`)
SUBPARTITION BY LINEAR HASH (`usd_month`)
SUBPARTITIONS 12
(
PARTITION y20 VALUES LESS THAN (2021)ENGINE = InnoDB,
PARTITION y21 VALUES LESS THAN (2022)ENGINE = InnoDB,
PARTITION y22 VALUES LESS THAN (2023)ENGINE = InnoDB,
PARTITION y23 VALUES LESS THAN (2024)ENGINE = InnoDB,
 PARTITION y VALUES LESS THAN MAXVALUE
 );')->execute();
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%user_stat_day}}');
    }
}
