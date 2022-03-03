<?php

use yii\base\Exception;
use yii\base\NotSupportedException;
use yii\db\Migration;

/**
 * Class m220301_064746_create_tbl_event_list
 */
class m220301_064746_create_tbl_event_list extends Migration
{
    /**
     * @return void
     * @throws Exception
     * @throws NotSupportedException
     */
    public function safeUp()
    {

        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
        }

//        if ($this->db->getTableSchema('{{%' . $tableName . '}}', true) !== null) {
//            $this->dropTable('{{%' . $tableName . '}}');
//        }

        $this->createTable('{{%event_list}}', [
            'el_id' => $this->primaryKey(),
            'el_key' => $this->string(500)->notNull(),
//            'el_name' => $this->string(255),
            'el_category' => $this->string(255),
            'el_description' => $this->string(1000),
            'el_enable_type' => $this->tinyInteger(1)->defaultValue(0)->notNull(),
            'el_enable_log' => $this->boolean()->defaultValue(false)->notNull(),
            'el_break' => $this->boolean()->defaultValue(false)->notNull(),
            'el_sort_order' => $this->integer()->defaultValue(0)->notNull(),
            'el_cron_expression' => $this->string(255)->defaultValue('* * * * *'),
            'el_condition' => $this->text(),
            'el_builder_json' => $this->json(),
            'el_updated_dt' => $this->dateTime(),
            'el_updated_user_id' => $this->integer(),
        ], $tableOptions);

        $this->addForeignKey(
            'FK-event_list-el_updated_user_id',
            '{{%event_list}}',
            'el_updated_user_id',
            '{{%employees}}',
            'id',
            'SET NULL',
            'CASCADE'
        );

        $this->createIndex('IND-event_list-el_key', '{{%event_list}}', ['el_key']);
        $this->createIndex('IND-event_list-el_sort_order', '{{%event_list}}', ['el_sort_order']);

        $this->createTable('{{%event_handler}}', [
            'eh_id' => $this->primaryKey(),
            'eh_el_id' => $this->integer()->notNull(),
            'eh_class' => $this->string(500)->notNull(),
            'eh_method' => $this->string(255)->notNull(),
//            'eh_name' => $this->string(255),
            'eh_enable_type' => $this->tinyInteger(1)->defaultValue(0)->notNull(),
            'eh_enable_log' => $this->boolean()->defaultValue(false)->notNull(),
            'eh_asynch' => $this->boolean()->defaultValue(false)->notNull(),
            'eh_break' => $this->boolean()->defaultValue(false)->notNull(),
            'eh_sort_order' => $this->integer()->defaultValue(0)->notNull(),
            'eh_cron_expression' => $this->string(255)->defaultValue('* * * * *'),
            'eh_condition' => $this->text(),
            'eh_params' => $this->json(),
            'eh_builder_json' => $this->json(),
            'eh_updated_dt' => $this->dateTime(),
            'eh_updated_user_id' => $this->integer(),
        ], $tableOptions);


        $this->addForeignKey(
            'FK-event_handler-eh_el_id',
            '{{%event_handler}}',
            'eh_el_id',
            '{{%event_list}}',
            'el_id',
            'CASCADE',
            'CASCADE'
        );

        $this->addForeignKey(
            'FK-event_handler-eh_updated_user_id',
            '{{%event_handler}}',
            'eh_updated_user_id',
            '{{%employees}}',
            'id',
            'SET NULL',
            'CASCADE'
        );

        $this->createIndex('IND-event_handler-eh_class-eh_method', '{{%event_handler}}', ['eh_class', 'eh_method']);
        $this->createIndex('IND-event_handler-eh_sort_order', '{{%event_handler}}', ['eh_sort_order']);
    }

    /**
     * @return void
     * @throws NotSupportedException
     */
    public function safeDown()
    {
        $this->dropTable('{{%event_handler}}');
        $this->dropTable('{{%event_list}}');
        Yii::$app->db->getSchema()->refreshTableSchema('{{%event_handler}}');
        Yii::$app->db->getSchema()->refreshTableSchema('{{%event_list}}');
    }
}
