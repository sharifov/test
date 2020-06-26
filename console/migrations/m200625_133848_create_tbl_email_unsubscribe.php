<?php

use yii\db\Migration;

/**
 * Class m200625_133848_create_tbl_email_unsubscribe
 */
class m200625_133848_create_tbl_email_unsubscribe extends Migration
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

        $this->createTable('email_unsubscribe', [
            'eu_email' => $this->string(160),
            'eu_project_id' => $this->integer(),
            'eu_created_user_id' => $this->integer(),
            'eu_created_dt' => $this->dateTime(),
        ], $tableOptions);

        $this->addPrimaryKey('email_unsubscribe_pk', '{{%email_unsubscribe}}', ['eu_email', 'eu_project_id']);

        $this->addColumn('{{%client_project}}', 'cp_unsubscribe', $this->boolean()->defaultValue(false));

       /* $this->addForeignKey('FK-client_project-cp_project_id_fkey',
            '{{%email_unsubscribe}}', 'eu_project_id',
            '{{%client_project}}', 'cp_project_id','CASCADE');*/
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%email_unsubscribe}}');
        $this->dropColumn('{{%client_project}}', 'cp_unsubscribe');
    }
}
