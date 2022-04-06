<?php

namespace modules\requestControl\migrations;

use yii\db\Migration;

/**
 * Handles the creation of table `{{%request_control_rule}}`.
 */
class m220331_122821_create_request_control_rule_table extends Migration
{
    private const TABLE_NAME = 'request_control_rule';
    private const TABLE = "{{%" . self::TABLE_NAME . "}}";

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
            'rcr_id' => $this->primaryKey(),
            'rcr_type' => $this->string(50)->notNull()->comment('type of rule'),
            'rcr_subject' => $this->string(255)->notNull()->comment('checking subject'),
            'rcr_local' => $this->integer()->defaultValue(0)->notNull()->comment('available request count to current resource per period'),
            'rcr_global' => $this->integer()->defaultValue(0)->notNull()->comment('available request count to system per period')
        ], $tableOptions);

        $this->createIndex('IND-request_control_rule-rcr_type-rcr_subject', self::TABLE, ['rcr_type', 'rcr_subject'], true);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropIndex('IND-request_control_rule-rcr_type-rcr_subject', self::TABLE);
        $this->dropTable(self::TABLE);
    }
}
