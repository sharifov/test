<?php

use yii\db\Migration;

/**
 * Class m190813_121749_create_permission_cases_for_admin
 */
class m190813_121749_create_permission_cases_for_admin extends Migration
{
    public  $routes = ['/cases/*', '/cases-category/*', '/cases-status-log/*'];

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $auth = Yii::$app->authManager;
        $admin = $auth->getRole('admin');
        foreach ($this->routes as $route) {
            $permission = $auth->createPermission($route);
            $auth->add($permission);
            $auth->addChild($admin, $permission);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $auth = Yii::$app->authManager;
        foreach ($this->routes as $route) {
            $permission = $auth->getPermission($route);
            $auth->remove($permission);
        }
    }

}
