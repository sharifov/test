<?php

use yii\db\Migration;

/**
 * Class m220408_103413_create_tbl_quote_communication
 */
class m220408_103413_create_tbl_quote_communication extends Migration
{
    const TABLE_NAME = 'quote_communication';
    const TABLE = '{{%' . self::TABLE_NAME . '}}';

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable(self::TABLE, [
            'qc_id' => $this->primaryKey(),
            'qc_communication_type' => $this->integer()->notNull(),
            'qc_communication_id' => $this->integer()->notNull(),
            'qc_quote_id' => $this->integer()->notNull(),
            'qc_created_dt' => $this->timestamp(),
            'qc_created_by' => $this->integer()
        ], $tableOptions);

        $this->addForeignKey(
            'FK-' . self::TABLE_NAME . '-qc_quote_id',
            self::TABLE,
            'qc_quote_id',
            '{{%quotes}}',
            'id',
            'CASCADE',
            'CASCADE'
        );
        $this->addForeignKey(
            'FK-' . self::TABLE_NAME . '-qc_created_by',
            self::TABLE,
            'qc_created_by',
            '{{%employees}}',
            'id',
            'SET NULL',
            'CASCADE'
        );

        $this->createIndex('IND-' . self::TABLE_NAME . '-qc_communication_type', self::TABLE, 'qc_communication_type');
        $this->createIndex('IND-' . self::TABLE_NAME . '-qc_communication_id', self::TABLE, 'qc_communication_id');
        $this->createIndex('IND-' . self::TABLE_NAME . '-qc_quote_id', self::TABLE, 'qc_quote_id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable(self::TABLE);
    }
}
