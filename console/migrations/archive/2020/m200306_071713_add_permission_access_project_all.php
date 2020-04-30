<?php

use common\models\Employee;
use yii\db\Migration;

/**
 * Class m200306_071713_add_permission_access_project_all
 */
class m200306_071713_add_permission_access_project_all extends Migration
{
    public $roles = [
        Employee::ROLE_ADMIN,
        Employee::ROLE_SUPER_ADMIN,
    ];

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $auth  = Yii::$app->authManager;

        $permission = $auth->createPermission('access/project/all');
        $permission->description = 'Access to all projects';
        $auth->add($permission);

        foreach ($this->roles as $item) {
            if ($role = $auth->getRole($item)) {
                $auth->addChild($role, $permission);
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
        $auth  = Yii::$app->authManager;

        if ($permission = $auth->getPermission('access/project/all')) {
            $auth->remove($permission);
        }

        if (Yii::$app->cache) {
            Yii::$app->cache->flush();
        }
    }
}
