<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%object_segment_rules}}`.
 */
class m220418_080119_create_object_segment_rules_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        try {
            $tableOptions = null;
            if ($this->db->driverName === 'mysql') {
                $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
            }
            $this->createTable('{{%object_segment_rules}}', [
                'osr_id'                  => $this->primaryKey(),
                'osr_osl_id'              => $this->integer(),
                'osr_title'               => $this->string(100)->notNull(),
                'osr_rule_condition'      => $this->string(1000)->notNull(),
                'osr_rule_condition_json' => $this->json(),
                'osr_enabled'             => $this->boolean()->defaultValue(false),
                'osr_updated_user_id'     => $this->integer()->unsigned()->null(),
                'osr_created_dt'          => $this->dateTime(),
                'osr_updated_dt'          => $this->dateTime(),
            ], $tableOptions);
            $this->addForeignKey(
                'FK-object_segment_rules-osr_osl_id',
                '{{%object_segment_rules}}',
                'osr_osl_id',
                '{{%object_segment_list}}',
                'osl_id',
                'CASCADE',
                'CASCADE'
            );
        } catch (\Throwable $throwable) {
            \Yii::error(
                \src\helpers\app\AppHelper::throwableLog($throwable),
                'm220418_080119_create_object_segment_rules_table:safeUp:Throwable'
            );
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        try {
            $this->dropTable('{{%object_segment_rules}}');

            if (Yii::$app->cache) {
                Yii::$app->cache->flush();
            }
        } catch (\Throwable $throwable) {
            \Yii::error(
                \src\helpers\app\AppHelper::throwableLog($throwable),
                'm220418_080119_create_object_segment_rules_table:safeDown:Throwable'
            );
        }
    }
}
