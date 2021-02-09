<?php

use common\models\Employee;
use yii\db\Migration;

/**
 * Class m201218_085808_add_permission_client_locale
 */
class m201218_085808_add_permission_client_locale extends Migration
{
    private $permissionName = 'global/client/locale/edit';

    public $roles = [
        Employee::ROLE_SUPER_ADMIN,
        Employee::ROLE_ADMIN,
    ];

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $auth = Yii::$app->authManager;

        $editLocale = $auth->createPermission($this->permissionName);
        $editLocale->description = 'Edit Client Locale';
        $auth->add($editLocale);

        foreach ($this->roles as $item) {
            if ($role = $auth->getRole($item)) {
                $auth->addChild($role, $editLocale);
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $auth = Yii::$app->authManager;

        if ($permission = $auth->getPermission($this->permissionName)) {
            $auth->remove($permission);
        }
    }
}
