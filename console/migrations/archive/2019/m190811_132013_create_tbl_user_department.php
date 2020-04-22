<?php

use yii\db\Migration;

/**
 * Class m190811_132013_create_tbl_user_department
 */
class m190811_132013_create_tbl_user_department extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%user_department}}', [
            'ud_user_id' => $this->integer()->notNull(),
            'ud_dep_id' => $this->integer()->notNull(),
            'ud_updated_dt' => $this->dateTime(),
        ], $tableOptions);

        $this->addPrimaryKey('PK-user_department', '{{%user_department}}', ['ud_user_id', 'ud_dep_id']);
        $this->addForeignKey('FK-user_department_ud_user_id', '{{%user_department}}', ['ud_user_id'], '{{%employees}}', ['id'], 'CASCADE', 'CASCADE');
        $this->addForeignKey('FK-user_department_ud_dep_id', '{{%user_department}}', ['ud_dep_id'], '{{%department}}', ['dep_id'], 'CASCADE', 'CASCADE');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%user_department}}');
    }
}
