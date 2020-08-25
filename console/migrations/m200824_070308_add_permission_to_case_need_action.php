<?php

use yii\db\Migration;
use yii\helpers\Inflector;

/**
 * Class m200824_070308_add_permission_to_case_need_action
 */
class m200824_070308_add_permission_to_case_need_action extends Migration
{
    private $permissionNames = [
        'Case List Owner',
        'Case List Empty',
        'Case List Group',
        'Case List Any',
    ];

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $auth = Yii::$app->authManager;

        $admin = $auth->getRole('admin');

        foreach ($this->permissionNames as $name) {
            $permission = $auth->createPermission(Inflector::variablize($name));
            $permission->description = $name;
            $auth->add($permission);

            $auth->addChild($admin, $permission);
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

        foreach ($this->permissionNames as $name) {
            if ($permission = $auth->getPermission(Inflector::variablize($name))) {
                $auth->remove($permission);
            }
        }

        if (Yii::$app->cache) {
            Yii::$app->cache->flush();
        }
    }
}
