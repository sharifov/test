<?php

use yii\db\Migration;

/**
 * Class m210505_085046_add_permission_add_quote_project_relations
 */
class m210505_085046_add_permission_add_quote_project_relations extends Migration
{
    public $roles = [
        \common\models\Employee::ROLE_ADMIN,
        \common\models\Employee::ROLE_SUPER_ADMIN,
    ];

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $auth = Yii::$app->authManager;

        $viewTransaction = $auth->createPermission('quote/addQuote/projectRelations');
        $viewTransaction->description = 'Add Quote to Project Relations';
        $auth->add($viewTransaction);

        foreach ($this->roles as $item) {
            if ($role = $auth->getRole($item)) {
                $auth->addChild($role, $viewTransaction);
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $auth = Yii::$app->authManager;

        foreach (['quote/addQuote/projectRelations'] as $permissionName) {
            if ($permission = $auth->getPermission($permissionName)) {
                $auth->remove($permission);
            }
        }
    }
}
