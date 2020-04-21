<?php

use yii\db\Migration;

/**
 * Class m190823_055939_create_tbl_call_user_access
 */
class m190823_055939_create_tbl_call_user_access extends Migration
{

    public $routes = [
        '/call-user-access/*'
    ];

    public $roles = [
        'admin' //, 'agent', 'supervision', 'ex_agent', 'ex_super', 'sup_agent', 'sup_super'
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

        $this->createTable('{{%call_user_access}}', [
            'cua_call_id' => $this->integer()->notNull(),
            'cua_user_id' => $this->integer()->notNull(),
            'cua_status_id' => $this->smallInteger(),
            'cua_created_dt' => $this->dateTime(),
            'cua_updated_dt' => $this->dateTime(),
        ], $tableOptions);

        $this->addPrimaryKey('PK-call_user_access', '{{%call_user_access}}', ['cua_call_id', 'cua_user_id']);
        $this->addForeignKey('FK-call_user_access_cua_call_id', '{{%call_user_access}}', ['cua_call_id'], '{{%call}}', ['c_id'], 'CASCADE', 'CASCADE');
        $this->addForeignKey('FK-call_user_access_cua_user_id', '{{%call_user_access}}', ['cua_user_id'], '{{%employees}}', ['id'], 'CASCADE', 'CASCADE');
        $this->createIndex('IND-call_user_access_cua_status_id', '{{%call_user_access}}', ['cua_status_id']);

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
        $this->dropForeignKey('FK-call_user_access_cua_call_id', '{{%call_user_access}}');
        $this->dropForeignKey('FK-call_user_access_cua_user_id', '{{%call_user_access}}');
        $this->dropTable('{{%call_user_access}}');

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
