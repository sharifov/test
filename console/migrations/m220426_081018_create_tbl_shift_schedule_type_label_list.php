<?php

use yii\db\Migration;

/**
 * Class m220426_081018_create_tbl_shift_schedule_type_label_list
 */
class m220426_081018_create_tbl_shift_schedule_type_label_list extends Migration
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

        $this->createTable('{{%shift_schedule_type_label}}', [
            'stl_key' => $this->string(100)->notNull()->unique(),
            'stl_name' => $this->string(100)->notNull(),
            'stl_enabled' => $this->boolean()->notNull()->defaultValue(true),
            'stl_color' => $this->string(20),
            'stl_icon_class' => $this->string(50),
            'stl_params_json' => $this->json(),
            'stl_sort_order' => $this->smallInteger()->defaultValue(0),
            'stl_updated_dt' => $this->dateTime(),
            'stl_updated_user_id' => $this->integer()
        ], $tableOptions);

        $this->addPrimaryKey(
            'PK-shift_schedule_type_label-stl_key',
            '{{%shift_schedule_type_label}}',
            ['stl_key']
        );

        $this->addForeignKey(
            'FK-shift_schedule_type_label-stl_updated_user_id',
            '{{%shift_schedule_type_label}}',
            'stl_updated_user_id',
            '{{%employees}}',
            'id',
            'SET NULL',
            'CASCADE'
        );

        $this->createIndex(
            'IND-shift_schedule_type_label-stl_enabled',
            '{{%shift_schedule_type_label}}',
            'stl_enabled'
        );
//        $this->createIndex(
//            'IND-shift_schedule_type_label-stl_sort_order',
//            '{{%shift_schedule_type_label}}',
//            'stl_sort_order'
//        );


        $this->createTable('{{%shift_schedule_type_label_assign}}', [
            'tla_stl_key' => $this->string(100)->notNull(),
            'tla_sst_id' => $this->integer()->notNull(),
            'tla_created_dt' => $this->dateTime()
        ], $tableOptions);


        $this->addPrimaryKey(
            'PK-shift_schedule_type_label_assign-stl_key-sst_id',
            '{{%shift_schedule_type_label_assign}}',
            ['tla_stl_key', 'tla_sst_id']
        );



        $this->addForeignKey(
            'FK-shift_schedule_type_label_assign-tla_stl_key',
            '{{%shift_schedule_type_label_assign}}',
            'tla_stl_key',
            '{{%shift_schedule_type_label}}',
            'stl_key',
            'CASCADE',
            'CASCADE'
        );

        $this->addForeignKey(
            'FK-shift_schedule_type_label_assign-sst_id',
            '{{%shift_schedule_type_label_assign}}',
            'tla_sst_id',
            '{{%shift_schedule_type}}',
            'sst_id',
            'CASCADE',
            'CASCADE'
        );

        //Yii::$app->db->getSchema()->refreshTableSchema('{{%shift_schedule_type}}');
    }

    /**
     * @return void
     */
    public function safeDown()
    {
        $this->dropTable('{{%shift_schedule_type_label_assign}}');
        $this->dropTable('{{%shift_schedule_type_label}}');
        // Yii::$app->db->getSchema()->refreshTableSchema('{{%shift_schedule_type_label}}');
    }
}
