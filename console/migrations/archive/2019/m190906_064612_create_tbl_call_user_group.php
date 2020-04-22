<?php

use yii\db\Migration;

/**
 * Class m190906_064612_create_tbl_call_user_group
 */
class m190906_064612_create_tbl_call_user_group extends Migration
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


        $this->createTable('{{%call_user_group}}',	[
            'cug_c_id'          => $this->integer()->notNull(),
            'cug_ug_id'         => $this->integer()->notNull(),
            'cug_created_dt'      => $this->dateTime(),
        ], $tableOptions);

        $this->addPrimaryKey('PK-call_user_group', '{{%call_user_group}}', ['cug_c_id', 'cug_ug_id']);
        $this->addForeignKey('FK-call_user_group_cug_c_id', '{{%call_user_group}}', ['cug_c_id'], '{{%call}}', ['c_id'], 'CASCADE', 'CASCADE');
        $this->addForeignKey('FK-call_user_group_cug_ug_id', '{{%call_user_group}}', ['cug_ug_id'], '{{%user_group}}', ['ug_id'], 'CASCADE', 'CASCADE');


        $this->createTable('{{%department_phone_project_user_group}}',	[
            'dug_dpp_id'        => $this->integer()->notNull(),
            'dug_ug_id'         => $this->integer()->notNull(),
            'dug_created_dt'    => $this->dateTime(),
        ], $tableOptions);

        $this->addPrimaryKey('PK-department_phone_project_user_group', '{{%department_phone_project_user_group}}', ['dug_dpp_id', 'dug_ug_id']);
        $this->addForeignKey('FK-department_phone_project_user_group_dug_dpp_id', '{{%department_phone_project_user_group}}', ['dug_dpp_id'], '{{%department_phone_project}}', ['dpp_id'], 'CASCADE', 'CASCADE');
        $this->addForeignKey('FK-department_phone_project_user_group_dug_ug_id', '{{%department_phone_project_user_group}}', ['dug_ug_id'], '{{%user_group}}', ['ug_id'], 'CASCADE', 'CASCADE');

        if (Yii::$app->cache) {
            Yii::$app->cache->flush();
        }

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('FK-call_user_group_cug_c_id', '{{%call_user_group}}');
        $this->dropForeignKey('FK-call_user_group_cug_ug_id', '{{%call_user_group}}');

        $this->dropTable('{{%call_user_group}}');

        $this->dropForeignKey('FK-department_phone_project_user_group_dug_dpp_id', '{{%department_phone_project_user_group}}');
        $this->dropForeignKey('FK-department_phone_project_user_group_dug_ug_id', '{{%department_phone_project_user_group}}');

        $this->dropTable('{{%department_phone_project_user_group}}');

        if (Yii::$app->cache) {
            Yii::$app->cache->flush();
        }
    }
}
