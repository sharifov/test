<?php

use yii\db\Migration;
use common\models\Employee;

/**
 * Class m191021_090906_create_tbl_conference
 */
class m191021_090906_create_tbl_conference extends Migration
{
    public $routes = [
        '/conference/*',
        '/conference-room/*',
    ];

    public $roles = [
        Employee::ROLE_ADMIN,
        Employee::ROLE_SUPER_ADMIN,
    ];

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%conference_room}}',	[
            'cr_id'                     => $this->primaryKey(),
            'cr_key'                    => $this->string(30)->notNull()->unique(),
            'cr_name'                   => $this->string(50)->notNull(),
            'cr_phone_number'           => $this->string(18)->notNull(),
            'cr_enable'                 => $this->boolean()->defaultValue(true),
            'cr_start_dt'               => $this->dateTime(),
            'cr_end_dt'                 => $this->dateTime(),

            'cr_param_muted'                        => $this->boolean()->defaultValue(false),
            'cr_param_beep'                         => $this->string(10)->defaultValue('true'),
            'cr_param_start_conference_on_enter'    => $this->boolean()->defaultValue(true),
            'cr_param_end_conference_on_enter'      => $this->boolean()->defaultValue(false),
            'cr_param_max_participants'             => $this->smallInteger()->defaultValue(250),
            'cr_param_record'                       => $this->string(20)->defaultValue('record-from-start'),
            'cr_param_region'                       => $this->string(3),
            'cr_param_trim'                         => $this->string(15)->defaultValue('trim-silence'),
            'cr_param_wait_url'                     => $this->string(255),
            'cr_moderator_phone_number'             => $this->string(18),

            'cr_created_dt'             => $this->dateTime(),
            'cr_updated_dt'             => $this->dateTime(),
            'cr_created_user_id'        => $this->integer(),
            'cr_updated_user_id'        => $this->integer(),

        ], $tableOptions);


        $this->addPrimaryKey('PK-qcall_config', '{{%qcall_config}}', ['qc_status_id', 'qc_call_att']);

        $this->addForeignKey(
            'FK-conference_room_cr_created_user_id',
            '{{%conference_room}}',
            'cr_created_user_id',
            '{{%employees}}',
            'id',
            'SET NULL',
            'CASCADE'
        );

        $this->addForeignKey(
            'FK-conference_room_cr_updated_user_id',
            '{{%conference_room}}',
            'cr_updated_user_id',
            '{{%employees}}',
            'id',
            'SET NULL',
            'CASCADE'
        );


        $this->createTable('{{%conference}}',	[
            'cf_id'                 => $this->primaryKey(),
            'cf_cr_id'              => $this->integer()->notNull(),
            'cf_sid'                => $this->string(34)->unique(),
            'cf_status_id'          => $this->smallInteger(),
            'cf_options'            => $this->text(),
            'cf_created_dt'         => $this->dateTime(),
            'cf_updated_dt'         => $this->dateTime(),
        ], $tableOptions);

        $this->addForeignKey('FK-conference_cf_cr_id', '{{%conference}}', ['cf_cr_id'], '{{%conference_room}}', ['cr_id'], 'CASCADE', 'CASCADE');


        $auth = Yii::$app->authManager;

        foreach ($this->routes as $route) {

            $permission = $auth->getPermission($route);
            if(!$permission) {
                $permission = $auth->createPermission($route);
                $auth->add($permission);
            }

            foreach ($this->roles as $role) {
                if (!$auth->hasChild($auth->getRole($role), $permission)) {
                    $auth->addChild($auth->getRole($role), $permission);
                }
            }
        }


        if (Yii::$app->cache) {
            Yii::$app->cache->flush();
        }

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {

        $this->dropTable('{{%lead_qcall}}');
        $this->dropTable('{{%qcall_config}}');


        $auth = Yii::$app->authManager;

        foreach ($this->routes as $route) {
            foreach ($this->roles as $role) {
                if ($permission = $auth->getPermission($route)) {
                    //$auth->remove($permission);
                    if ($auth->hasChild($auth->getRole($role), $permission)) {
                        $auth->removeChild($auth->getRole($role), $permission);
                    }
                }
            }
        }


        if (Yii::$app->cache) {
            Yii::$app->cache->flush();
        }
    }
}
