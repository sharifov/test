<?php

use yii\db\Migration;

/**
 * Class m211006_144039_create_tbl_user_data
 */
class m211006_144039_create_tbl_user_data extends Migration
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
        $this->createTable('{{%user_data}}', [
            'ud_user_id' => $this->integer()->notNull(),
            'ud_key' => $this->integer()->notNull(),
            'ud_value' => $this->string(20),
            'ud_updated_dt' => $this->dateTime(),
        ], $tableOptions);

        $this->addPrimaryKey('PK-user_data', '{{%user_data}}', ['ud_user_id', 'ud_key']);
        $this->addForeignKey(
            'FK-user_data-ud_user_id',
            '{{%user_data}}',
            ['ud_user_id'],
            '{{%employees}}',
            ['id'],
            'CASCADE',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%user_data}}');
    }
}
