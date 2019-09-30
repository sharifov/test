<?php

use yii\db\Migration;
use common\models\Employee;

/**
 * Class m190930_080157_create_tbl_qcall_config
 */
class m190930_080157_create_tbl_qcall_config extends Migration
{

//    public $roles = [
//        Employee::ROLE_ADMIN,
//        Employee::ROLE_SUPER_ADMIN,
//        Employee::ROLE_AGENT, Employee::ROLE_SUPERVISION,
//        Employee::ROLE_EX_AGENT, Employee::ROLE_EX_SUPER,
//        Employee::ROLE_SUP_AGENT, Employee::ROLE_SUP_SUPER,
//        Employee::ROLE_QA,
//        Employee::ROLE_USER_MANAGER
//    ];

    public $routes = [
        '/lead_qcall/*',
    ];

    public $routes2 = [
        '/qcall_config/*',
    ];

    public $roles = [
        Employee::ROLE_ADMIN,
        Employee::ROLE_SUPER_ADMIN,
        Employee::ROLE_AGENT, Employee::ROLE_SUPERVISION,
    ];

    public $roles2 = [
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


        $this->createTable('{{%qcall_config}}',	[
            'qc_status_id'              => $this->integer()->notNull(),
            'qc_call_att'               => $this->integer()->notNull(),
            'qt_client_time_enable'     => $this->boolean()->defaultValue(false),
            'qt_time_from'              => $this->integer()->notNull(),
            'qt_time_to'                => $this->integer()->notNull(),
            'qt_created_dt'             => $this->dateTime(),
            'qt_updated_dt'             => $this->dateTime(),
            'qt_created_user_id'        => $this->integer(),
            'qt_updated_user_id'        => $this->integer(),

        ], $tableOptions);

        $this->addPrimaryKey('PK-qcall_config', '{{%qcall_config}}', ['qc_status_id', 'qc_call_att', 'qt_client_time_enable']);

        $this->addForeignKey(
            'FK-qcall_config_qt_created_user_id',
            '{{%qcall_config}}',
            'qt_created_user_id',
            '{{%employees}}',
            'id',
            'SET NULL',
            'CASCADE'
        );

        $this->addForeignKey(
            'FK-qcall_config_qt_updated_user_id',
            '{{%qcall_config}}',
            'qt_updated_user_id',
            '{{%employees}}',
            'id',
            'SET NULL',
            'CASCADE'
        );



        $this->createTable('{{%lead_qcall}}',	[
            'lqc_lead_id'   => $this->integer()->notNull(),
            'lqc_dt_from'   => $this->dateTime()->notNull(),
            'lqc_dt_to'     => $this->dateTime()->notNull(),
        ], $tableOptions);

        $this->addPrimaryKey('PK-lead_qcall_lqc_lead_id', '{{%lead_qcall}}', ['lqc_lead_id']);


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

        foreach ($this->routes2 as $route) {

            $permission = $auth->getPermission($route);
            if(!$permission) {
                $permission = $auth->createPermission($route);
                $auth->add($permission);
            }

            foreach ($this->roles2 as $role) {
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

        foreach ($this->routes2 as $route) {
            foreach ($this->roles2 as $role) {
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
