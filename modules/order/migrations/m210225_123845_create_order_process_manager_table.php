<?php

namespace modules\order\migrations;

use yii\db\Migration;

/**
 * Handles the creation of table `{{%order_process_manager}}`.
 */
class m210225_123845_create_order_process_manager_table extends Migration
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

        $this->createTable('{{%order_process_manager}}', [
            'opm_id' => $this->integer(),
            'opm_status' => $this->tinyInteger()->notNull(),
            'opm_created_dt' => $this->dateTime(),
        ], $tableOptions);

        $this->addPrimaryKey('PK-order_process_manager-opm_id', '{{%order_process_manager}}', ['opm_id']);

        $this->addForeignKey(
            'FK-order_process_manager-opm_id',
            '{{%order_process_manager}}',
            ['opm_id'],
            '{{%order}}',
            'or_id',
            'CASCADE',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%order_process_manager}}');
    }
}
