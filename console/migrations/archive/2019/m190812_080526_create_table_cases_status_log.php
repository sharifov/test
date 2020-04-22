<?php

use yii\db\Migration;

/**
 * Class m190812_080526_create_table_cases_status_log
 */
class m190812_080526_create_table_cases_status_log extends Migration
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

        $this->createTable('{{%cases_status_log}}', [
            'csl_id' => $this->primaryKey(),
            'csl_case_id' => $this->integer()->notNull(),
            'csl_from_status' => $this->integer(),
            'csl_to_status' => $this->integer()->notNull(),
            'csl_start_dt' => $this->dateTime()->notNull(),
            'csl_end_dt' => $this->dateTime(),
            'csl_time_duration' => $this->integer(),
            'csl_created_user_id' => $this->integer(),
            'csl_owner_id' => $this->integer(),
            'csl_description' => $this->text(),
        ], $tableOptions);

        $this->addForeignKey(
            'FK-cases_status_log_csl_case_id',
            '{{%cases_status_log}}',
            'csl_case_id',
            '{{%cases}}',
            'cs_id',
            'CASCADE',
            'CASCADE'
        );

        $this->addForeignKey(
            'FK-cases_status_log_csl_owner_id',
            '{{%cases_status_log}}',
            'csl_owner_id',
            '{{%employees}}',
            'id',
            'SET NULL',
            'CASCADE'
        );

        if (Yii::$app->cache) {
            Yii::$app->cache->flush();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('FK-cases_status_log_csl_owner_id', '{{%cases_status_log}}');
        $this->dropForeignKey('FK-cases_status_log_csl_case_id', '{{%cases_status_log}}');
        $this->dropTable('{{%cases_status_log}}');

        if (Yii::$app->cache) {
            Yii::$app->cache->flush();
        }
    }

}
