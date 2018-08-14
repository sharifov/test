<?php

use yii\db\Migration;

/**
 * Class m180814_070719_create_tbl_api_user
 */
class m180814_070719_create_tbl_api_user extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {

        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }

        $projects = [];
        $projects[] = ['id' => 1, 'name' => 'HOP2', 'api_username' => 'hop2', 'api_password' => '54d120d9d4e6489443ae33f9e08fefe3c1006dfdedf1574ae97d0589154a8250', 'email' => 'no-reply@hop2.com', 'project_id' => 1];
        $projects[] = ['id' => 2, 'name' => 'OVAGO', 'api_username' => 'ovago', 'api_password' => '038ce0121a1666678d4db57cb10e8667b98d8b08c408cdf7c9b04f1430071826', 'email' => 'no-reply@ovago.com', 'project_id' => 2];
        $projects[] = ['id' => 3, 'name' => 'WEFARE', 'api_username' => 'wefare', 'api_password' => 'dc83bdfb8b66644215c38eed04c47c2aa0ca5bd409a801b30b0c4d5d54bbb1b2', 'email' => 'assistant@wefare.com', 'project_id' => 3];
        $projects[] = ['id' => 4, 'name' => 'GURUFARE', 'api_username' => 'gurufare', 'api_password' => '2a1dd61a3baf61b22241de93d1d4c072f44391de407d7c26cecb98b50012e492', 'email' => null, 'project_id' => 4];
        $projects[] = ['id' => 5, 'name' => 'BUSINESSCLASS', 'api_username' => 'businessclass', 'api_password' => '57966012c9d9d871a7f3ef26a727fd06ca1a8cc06a51afb7adbdd4afc3f25c3e', 'email' => null, 'project_id' => 5];
        $projects[] = ['id' => 6, 'name' => 'WOWFARE', 'api_username' => 'wowfare', 'api_password' => 'd190c378e131ccfd8a889c8ee8994cb55f22fbeeb93f9b99007e8e7ecc24d0dd', 'email' => 'assistant@wowfare.com', 'project_id' => 6];
        $projects[] = ['id' => 7, 'name' => 'BackOffice', 'api_username' => 'backoffice', 'api_password' => 'bf_test2018', 'email' => null, 'project_id' => null];


        $this->createTable('{{%api_user}}', [
            'au_id'                 => $this->primaryKey(),
            'au_name'               => $this->string(100)->notNull(),
            'au_api_username'       => $this->string(100)->notNull()->unique(),
            'au_api_password'       => $this->string(100)->notNull(),
            'au_email'              => $this->string(160),
            'au_project_id'         => $this->integer(),
            'au_enabled'            => $this->boolean()->defaultValue(true),
            'au_updated_dt'         => $this->dateTime(),
            'au_updated_user_id'    => $this->integer(),
        ], $tableOptions);

        //$this->addForeignKey('api_user_au_updated_user_id_fkey', '{{%api_user}}', ['au_updated_user_id'], '{{%user}}', ['id'], 'SET NULL', 'CASCADE');
        $this->addForeignKey('api_user_au_project_id_fkey', '{{%api_user}}', ['au_project_id'], '{{%projects}}', ['id'], 'SET NULL', 'CASCADE');

        foreach ($projects as $k => $project) {
            $this->insert('{{%api_user}}', [
                'au_id'              => $project['id'],
                'au_name'            => $project['name'],
                'au_api_username'    => $project['api_username'],
                'au_api_password'    => sha1($project['api_password']),
                'au_email'           => $project['email'],
                'au_project_id'      => $project['project_id'],
                'au_enabled'         => true,
                'au_updated_dt'      => date('Y-m-d H:i:s'),
                'au_updated_user_id' => null
            ]);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%api_user}}');
    }


}
