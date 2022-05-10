<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%object_segment_list}}`.
 */
class m220418_074825_create_object_segment_list_table extends Migration
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
            $this->createTable('{{%object_segment_list}}', [
                'osl_id'              => $this->primaryKey(),
                'osl_ost_id'         => $this->integer(),
                'osl_key'             => $this->string(100)->notNull(),
                'osl_title'           => $this->string(100)->notNull(),
                'osl_description'     => $this->string(1000),
                'osl_enabled'         => $this->boolean()->defaultValue(false),
                'osl_updated_user_id' => $this->integer()->unsigned()->null(),
                'osl_created_dt'      => $this->dateTime(),
                'osl_updated_dt'      => $this->dateTime(),
            ], $tableOptions);
            $this->addForeignKey(
                'FK-object_segment_list-osl_ost_id',
                '{{%object_segment_list}}',
                'osl_ost_id',
                '{{%object_segment_types}}',
                'ost_id',
                'CASCADE',
                'CASCADE'
            );
            $this->createIndex(
                'IND-object_segment_list-osl_type_id-osl_key',
                '{{%object_segment_list}}',
                ['osl_ost_id', 'osl_key'],
                true
            );
        } catch (\Throwable $throwable) {
            \Yii::error(
                \src\helpers\app\AppHelper::throwableLog($throwable),
                'm220418_074825_create_object_segment_list_table:safeUp:Throwable'
            );
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        try {
            $this->dropTable('{{%object_segment_list}}');

            if (Yii::$app->cache) {
                Yii::$app->cache->flush();
            }
        } catch (\Throwable $throwable) {
            \Yii::error(
                \src\helpers\app\AppHelper::throwableLog($throwable),
                'm220418_074825_create_object_segment_list_table:safeDown:Throwable'
            );
        }
    }
}
