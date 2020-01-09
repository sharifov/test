<?php

use yii\db\Migration;

/**
 * Class m200109_131601_add_permission_to_setting_category
 */
class m200109_131601_add_permission_to_setting_category extends Migration
{
    private $_roles = ['admin', 'superadmin'];
    private $_permissionName = '/setting-category/*';

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $auth = Yii::$app->authManager;

        $permission = $auth->createPermission($this->_permissionName);
        $auth->add($permission);

        foreach ($this->_roles as $item) {
            $role = $auth->getRole($item);
            $auth->addChild($role, $permission);
        }

        $this->_flush();
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $auth = Yii::$app->authManager;

        if ($permission = $auth->getPermission($this->_permissionName)) {
            $auth->remove($permission);
        }

        $this->_flush();
    }

    private function _flush(): void
    {
        if (Yii::$app->cache) {
            Yii::$app->cache->flush();
        }
    }
}
