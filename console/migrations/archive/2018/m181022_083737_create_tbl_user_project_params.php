<?php

use yii\db\Migration;

/**
 * Class m181022_083737_create_tbl_user_project_params
 */
class m181022_083737_create_tbl_user_project_params extends Migration
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

        $this->createTable('{{%user_project_params}}', [
            'upp_user_id' => $this->integer()->notNull(),
            'upp_project_id' => $this->integer()->notNull(),

            'upp_email' => $this->string(100),
            'upp_phone_number' => $this->string(30),
            'upp_tw_phone_number' => $this->string(30),
            'upp_tw_sip_id' => $this->string(100),

            'upp_created_dt' => $this->dateTime(),
            'upp_updated_dt' => $this->dateTime(),
            'upp_updated_user_id' => $this->integer(),
        ], $tableOptions);


        $this->addPrimaryKey('user_project_params_pk', '{{%user_project_params}}', ['upp_user_id', 'upp_project_id']);
        $this->addForeignKey('user_project_params_upp_project_id_fkey', '{{%user_project_params}}', ['upp_project_id'], '{{%projects}}', ['id'], 'CASCADE', 'CASCADE');

        $this->addForeignKey('user_project_params_upp_user_id_fkey', '{{%user_project_params}}', ['upp_user_id'], '{{%employees}}', ['id'], 'CASCADE', 'CASCADE');
        $this->addForeignKey('user_project_params_upp_updated_user_id_fkey', '{{%user_project_params}}', ['upp_updated_user_id'], '{{%employees}}', ['id'], 'SET NULL', 'CASCADE');


        $userParams = \common\models\EmployeeContactInfo::find()->all();

        if($userParams) {
            foreach ($userParams as $k => $param) {
                $this->insert('{{%user_project_params}}', [
                    'upp_user_id' => $param->employee_id,
                    'upp_project_id' => $param->project_id,
                    'upp_email' => $param->email_user,
                    'upp_phone_number' => str_replace([' ', '-'], ['', ''], $param->direct_line),
                    'upp_created_dt' => $param->created,
                    'upp_updated_dt' => $param->updated ?? date('Y-m-d H:i:s'),
                ]);
            }
        }

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%user_project_params}}');
    }
}
