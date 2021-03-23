<?php

namespace modules\attraction\migrations;

use yii\db\Migration;

/**
 * Class m210306_092015_create_tbl_attraction_pax
 */
class m210306_092015_create_tbl_attraction_pax extends Migration
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

        $this->createTable('{{%attraction_pax}}', [
            'atnp_id' => $this->primaryKey(),
            'atnp_atn_id' => $this->integer()->notNull(),
            'atnp_type_id' => $this->tinyInteger()->notNull(),
            'atnp_age' => $this->tinyInteger(2),
            'atnp_first_name' => $this->string(40),
            'atnp_last_name' => $this->string(40),
            'atnp_dob' => $this->date(),
        ], $tableOptions);

        $this->addForeignKey('FK-attraction_pax-atnp_atn_id', '{{%attraction_pax}}', ['atnp_atn_id'], '{{%attraction}}', ['atn_id'], 'CASCADE', 'CASCADE');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%attraction_pax}}');
    }
}
