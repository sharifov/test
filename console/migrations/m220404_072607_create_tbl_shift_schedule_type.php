<?php

use yii\db\Migration;

/**
 * Class m220404_072607_create_tbl_shift_schedule_type
 */
class m220404_072607_create_tbl_shift_schedule_type extends Migration
{
    /**
     * @return void
     * @throws \yii\base\Exception
     */
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%shift_schedule_type}}', [
            'sst_id' => $this->primaryKey(),
            'sst_key' => $this->string(100)->notNull()->unique(),
            'sst_name' => $this->string(100)->notNull(),
            'sst_title' => $this->string(255),
            'sst_enabled' => $this->boolean()->notNull()->defaultValue(true),
            'sst_readonly' => $this->boolean()->notNull()->defaultValue(false),
            'sst_work_time' => $this->boolean()->notNull()->defaultValue(true),
            'sst_color' => $this->string(20),
            'sst_icon_class' => $this->string(100),
            'sst_css_class' => $this->string(100),
            'sst_params_json' => $this->json(),
            'sst_sort_order' => $this->smallInteger()->defaultValue(0),
            'sst_updated_dt' => $this->dateTime(),
            'sst_updated_user_id' => $this->integer()
        ], $tableOptions);

        $this->addForeignKey(
            'FK-shift_schedule_type-sst_updated_user_id',
            '{{%shift_schedule_type}}',
            'sst_updated_user_id',
            '{{%employees}}',
            'id',
            'SET NULL',
            'CASCADE'
        );

        $this->createIndex('IND-shift_schedule_type-sst_enabled', '{{%shift_schedule_type}}', 'sst_enabled');
        $this->createIndex('IND-shift_schedule_type-sst_sort_order', '{{%shift_schedule_type}}', 'sst_sort_order');

        Yii::$app->db->getSchema()->refreshTableSchema('{{%shift_schedule_type}}');
    }

    /**
     * @return void
     * @throws \yii\base\NotSupportedException
     */
    public function safeDown()
    {
        $this->dropTable('{{%shift_schedule_type}}');
        Yii::$app->db->getSchema()->refreshTableSchema('{{%shift_schedule_type}}');
    }
}
