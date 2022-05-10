<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%object_segment_list}}`.
 */
class m220418_064632_create_object_segment_types_table extends Migration
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
            $this->createTable('{{%object_segment_types}}', [
                'ost_id'         => $this->primaryKey(),
                'ost_key'        => $this->string(100)->notNull(),
                'ost_created_dt' => $this->dateTime(),
            ], $tableOptions);
            $this->createIndex('IND-object_segment_types-ost_key', '{{%object_segment_types}}', 'ost_key', true);
        } catch (\Throwable $throwable) {
            \Yii::error(
                \src\helpers\app\AppHelper::throwableLog($throwable),
                'm220418_064632_create_object_segment_types_table:safeUp:Throwable'
            );
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        try {
            $this->dropTable('{{%object_segment_types}}');

            if (Yii::$app->cache) {
                Yii::$app->cache->flush();
            }
        } catch (\Throwable $throwable) {
            \Yii::error(
                \src\helpers\app\AppHelper::throwableLog($throwable),
                'm220418_064632_create_object_segment_types_table:safeDown:Throwable'
            );
        }
    }
}
