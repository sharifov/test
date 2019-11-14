<?php

use yii\db\Migration;

/**
 * Class m191114_080518_create_tbl_currency
 */
class m191114_080518_create_tbl_currency extends Migration
{
    public $routes = [
        '/currency/*',
    ];

    public $roles = [
        \common\models\Employee::ROLE_ADMIN,
        \common\models\Employee::ROLE_SUPER_ADMIN,
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


        $this->createTable('{{%currency}}',	[
            'cp_id'                 => $this->primaryKey(),
            'cp_cf_id'              => $this->integer()->notNull(),
            'cp_call_sid'           => $this->string(34)->unique(),
            'cp_call_id'            => $this->integer(),
            'cp_status_id'          => $this->smallInteger(),
            'cp_join_dt'            => $this->dateTime(),
            'cp_leave_dt'           => $this->dateTime(),
        ], $tableOptions);

        $this->addForeignKey('FK-conference_participant_cp_cf_id', '{{%conference_participant}}', ['cp_cf_id'], '{{%conference}}', ['cf_id'], 'CASCADE', 'CASCADE');
        $this->addForeignKey('FK-conference_participant_cp_call_id', '{{%conference_participant}}', ['cp_call_id'], '{{%call}}', ['c_id'], 'CASCADE', 'CASCADE');

        $this->createIndex('IND-conference_participant_cp_call_sid', '{{%conference_participant}}', ['cp_call_sid']);


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

        $this->dropTable('{{%conference_participant}}');


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
