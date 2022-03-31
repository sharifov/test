<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%request_control_rule}}`.
 */
class m220331_122821_create_request_control_rule_table extends Migration
{
    private const TABLE_NAME = 'request_control_rule';
    private const TABLE = "{{%". self::TABLE_NAME ."}}";

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql')
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';

        $this->createTable(self::TABLE, [
            'id' => $this->primaryKey(),
            'type' => $this->string(50)->notNull()->comment('type of rule'),
            'subject' => $this->string(255)->notNull()->comment('checking subject'),
            'local' => $this->integer()->defaultValue(0)->notNull()->comment('available request count to current resource per period'),
            'global' => $this->integer()->defaultValue(0)->notNull()->comment('available request count to system per period')
        ], $tableOptions);

        $this->createIndex(implode('__', ['unq', self::TABLE_NAME, 'type', 'subject']), self::TABLE, ['type', 'subject'],true);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropIndex(implode('__', ['unq', self::TABLE_NAME, 'type', 'subject']), self::TABLE);
        $this->dropTable(self::TABLE);
    }
}
