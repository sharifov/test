<?php

use common\models\Employee;
use yii\db\Migration;

/**
 * Class m210121_084517_add_permission_file_view
 */
class m210121_084517_add_permission_file_view extends Migration
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
        $auth = Yii::$app->authManager;

        $leadView = $auth->createPermission('file-storage/view');
        $leadView->description = 'File storage view';
        $auth->add($leadView);

        foreach ($this->roles as $item) {
            if ($role = $auth->getRole($item)) {
                $auth->addChild($role, $leadView);
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $auth = Yii::$app->authManager;

        $permissions = [
            'file-storage/view',
        ];

        foreach ($permissions as $permissionName) {
            if ($permission = $auth->getPermission($permissionName)) {
                $auth->remove($permission);
            }
        }
    }
}
