<?php

use yii\db\Migration;

/**
 * Class m190819_075930_add_permissions_for_route_create_from_case
 */
class m190819_075930_add_permissions_for_route_create_from_case extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $auth = Yii::$app->authManager;

        $roles = ['admin', 'ex_agent', 'ex_super'];

        $route = $auth->createPermission('/lead/create-from-case');
        $auth->add($route);
        foreach ($roles as $item) {
            $role = $auth->getRole($item);
            $auth->addChild($role, $route);
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
        $auth = Yii::$app->authManager;

        $route = $auth->getPermission('/lead/create-from-case');
        $auth->remove($route);

        if (Yii::$app->cache) {
            Yii::$app->cache->flush();
        }
    }

}
