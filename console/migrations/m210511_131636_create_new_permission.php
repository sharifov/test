<?php

use common\models\Employee;
use yii\db\Migration;

/**
 * Class m210511_131636_create_new_permission
 */
class m210511_131636_create_new_permission extends Migration
{
    public $roles = [
        Employee::ROLE_SUPER_ADMIN,
        Employee::ROLE_ADMIN,
        Employee::ROLE_AGENT,
        Employee::ROLE_SUPERVISION,
        Employee::ROLE_QA,
        Employee::ROLE_QA_SUPER,
        Employee::ROLE_USER_MANAGER,
        Employee::ROLE_SUP_AGENT,
        Employee::ROLE_SUP_SUPER,
        Employee::ROLE_EX_AGENT,
        Employee::ROLE_EX_SUPER,
        Employee::ROLE_SALES_SENIOR,
        Employee::ROLE_EXCHANGE_SENIOR,
        Employee::ROLE_SUPPORT_SENIOR,
    ];

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $auth = Yii::$app->authManager;

        $addBlockListPermission = $auth->createPermission('PhoneWidget_AddBlockList');
        $addBlockListPermission->description = 'Phone widget add phone to blacklist';
        $auth->add($addBlockListPermission);

        foreach ($this->roles as $item) {
            if ($role = $auth->getRole($item)) {
                $auth->addChild($role, $addBlockListPermission);
            }
        }

        Yii::$app->cache->flush();
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $auth = Yii::$app->authManager;

        if ($permission = $auth->getPermission('PhoneWidget_AddBlockList')) {
            $auth->remove($permission);
        }

        Yii::$app->cache->flush();
    }
}
