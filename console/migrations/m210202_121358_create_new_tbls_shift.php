<?php

use yii\db\Migration;

/**
 * Class m210202_121358_create_new_tbls_shift
 */
class m210202_121358_create_new_tbls_shift extends Migration
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

        $this->createTable('{{%shift}}', [
            'sh_id' => $this->primaryKey(),
            'sh_name' => $this->string(100)->notNull(),
            'sh_enabled' => $this->tinyInteger(1)->notNull(),
            'sh_color' => $this->string(15),
            'sh_sort_order' => $this->smallInteger(),
            'sh_created_dt' => $this->dateTime(),
            'sh_updated_dt' => $this->dateTime(),
            'sh_created_user_id' => $this->integer(),
            'sh_updated_user_id' => $this->integer()
        ], $tableOptions);

        $this->createTable('{{%shift_schedule_rule}}', [
            'ssr_id' => $this->primaryKey()->notNull(),
            'ssr_shift_id' => $this->integer()->notNull(),
            'ssr_title' => $this->string(),
            'ssr_timezone' => $this->string(100),
            'ssr_start_time_loc' => $this->time()->notNull(),
            'ssr_end_time_loc' => $this->time(),
            'ssr_duration_time' => $this->integer(),
            'ssr_cron_expression' => $this->string(100),
            'ssr_cron_expression_exclude' => $this->string(100),
            'ssr_enabled' => $this->tinyInteger(1)->notNull(),
            'ssr_start_time_utc' => $this->time()->notNull(),
            'ssr_end_time_utc' => $this->time(),
            'ssr_created_dt' => $this->dateTime(),
            'ssr_updated_dt' => $this->dateTime(),
            'ssr_created_user_id' => $this->integer(),
            'ssr_updated_user_id' => $this->integer()
        ], $tableOptions);

        $this->addForeignKey(
            'FK-shift_schedule_rule-ssr_shift_id',
            '{{%shift_schedule_rule}}',
            'ssr_shift_id',
            '{{%shift}}',
            'sh_id',
            'CASCADE',
            'CASCADE'
        );

        $this->createTable('{{%user_shift_assign}}', [
            'usa_user_id' => $this->integer()->notNull(),
            'usa_ssr_id' => $this->integer()->notNull(),
            'usa_created_dt' => $this->dateTime(),
            'usa_created_user_id' => $this->integer()
        ], $tableOptions);

        $this->addPrimaryKey('PK-user_shift_assign', '{{%user_shift_assign}}', [
            'usa_user_id', 'usa_ssr_id'
        ]);

        $this->addForeignKey(
            'FK-user_shift_assign-usa_user_id',
            '{{%user_shift_assign}}',
            'usa_user_id',
            '{{%employees}}',
            'id',
            'CASCADE',
            'CASCADE'
        );

        $this->addForeignKey(
            'FK-user_shift_assign-usa_ssr_id',
            '{{%user_shift_assign}}',
            'usa_ssr_id',
            '{{%shift_schedule_rule}}',
            'ssr_id',
            'CASCADE',
            'CASCADE'
        );

        $this->createTable('{{%user_shift_schedule}}', [
            'uss_id' => $this->primaryKey(),
            'uss_user_id' => $this->integer()->notNull(),
            'uss_shift_id' => $this->integer()->notNull(),
            'uss_ssr_id' => $this->integer(),
            'uss_description' => $this->string(500),
            'uss_start_utc_dt' => $this->dateTime()->notNull(),
            'uss_end_utc_dt' => $this->dateTime(),
            'uss_duration' => $this->integer(),
            'uss_status_id' => $this->tinyInteger()->notNull(),
            'uss_type_id' => $this->tinyInteger()->notNull(),
            'uss_customized' => $this->tinyInteger(),
            'uss_created_dt' => $this->dateTime(),
            'uss_updated_dt' => $this->dateTime(),
            'uss_created_user_id' => $this->integer(),
            'uss_updated_user_id' => $this->integer()
        ], $tableOptions);

        $this->addForeignKey(
            'FK-user_shift_schedule-uss_user_id',
            '{{%user_shift_schedule}}',
            'uss_user_id',
            '{{%employees}}',
            'id',
            'CASCADE',
            'CASCADE'
        );

        $this->addForeignKey(
            'FK-user_shift_schedule-uss_shift_id',
            '{{%user_shift_schedule}}',
            'uss_shift_id',
            '{{%shift}}',
            'sh_id',
            'CASCADE',
            'CASCADE'
        );

        $this->addForeignKey(
            'FK-user_shift_schedule-uss_ssr_id',
            '{{%user_shift_schedule}}',
            'uss_ssr_id',
            '{{%shift_schedule_rule}}',
            'ssr_id',
            'SET NULL',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%user_shift_schedule}}');
        $this->dropTable('{{%user_shift_assign}}');
        $this->dropTable('{{%shift_schedule_rule}}');
        $this->dropTable('{{%shift}}');
    }
}
