<?php

use common\models\Employee;
use sales\rbac\rules\clientChat\returnRule\ClientChatReturnRule;
use sales\rbac\rules\clientChat\take\ClientChatTakeHoldRule;
use sales\rbac\rules\clientChat\take\ClientChatTakeIdleRule;
use sales\rbac\rules\clientChat\take\ClientChatTakeInProgressRule;
use sales\rbac\rules\clientChat\take\ClientChatTakeRule;
use yii\db\Migration;

/**
 * Class m201015_092219_add_client_chat_take_permissions
 */
class m201015_092219_add_client_chat_take_permissions extends Migration
{
    private $roles = [
        Employee::ROLE_ADMIN,
        Employee::ROLE_SUPER_ADMIN,
        Employee::ROLE_AGENT,
        Employee::ROLE_SUPERVISION,
        Employee::ROLE_SUP_AGENT,
        Employee::ROLE_EX_AGENT,
        Employee::ROLE_EX_SUPER,
        Employee::ROLE_SALES_SENIOR,
        Employee::ROLE_EXCHANGE_SENIOR,
        Employee::ROLE_SUP_SUPER,
        Employee::ROLE_QA,
        Employee::ROLE_QA_SUPER,
        Employee::ROLE_SUPPORT_SENIOR,
        Employee::ROLE_USER_MANAGER,
    ];

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $auth = Yii::$app->authManager;

        $returnRule = new ClientChatReturnRule();
        $auth->add($returnRule);
        $returnPermission = $auth->createPermission('client-chat/return');
        $returnPermission->description = 'Client chat return';
        $returnPermission->ruleName = $returnRule->name;
        $auth->add($returnPermission);

        //

        $takeRule = new ClientChatTakeRule();
        $auth->add($takeRule);
        $takePermission = $auth->createPermission('client-chat/take');
        $takePermission->description = 'Client chat take';
        $takePermission->ruleName = $takeRule->name;
        $auth->add($takePermission);

        $takeInProgressRule = new ClientChatTakeInProgressRule();
        $auth->add($takeInProgressRule);
        $takeInProgressPermission = $auth->createPermission('client-chat/take/in_progress');
        $takeInProgressPermission->description = 'Client chat take in progress';
        $takeInProgressPermission->ruleName = $takeInProgressRule->name;
        $auth->add($takeInProgressPermission);
        $auth->addChild($takeInProgressPermission, $takePermission);

        $takeIdleRule = new ClientChatTakeIdleRule();
        $auth->add($takeIdleRule);
        $takeIdlePermission = $auth->createPermission('client-chat/take/idle');
        $takeIdlePermission->description = 'Client chat take idle';
        $takeIdlePermission->ruleName = $takeIdleRule->name;
        $auth->add($takeIdlePermission);
        $auth->addChild($takeIdlePermission, $takePermission);

        $takeHoldRule = new ClientChatTakeHoldRule();
        $auth->add($takeHoldRule);
        $takeHoldPermission = $auth->createPermission('client-chat/take/hold');
        $takeHoldPermission->description = 'Client chat take hold';
        $takeHoldPermission->ruleName = $takeHoldRule->name;
        $auth->add($takeHoldPermission);
        $auth->addChild($takeHoldPermission, $takePermission);

        //
        $this->addPermissionsToRole($returnPermission, $takePermission);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $auth = Yii::$app->authManager;

        $rules = [
            'ClientChatReturnRule',
            'ClientChatTakeRule',
            'ClientChatTakeInProgressRule',
            'ClientChatTakeIdleRule',
            'ClientChatTakeHoldRule',
        ];

        $permissions = [
            'client-chat/return',
            'client-chat/take',
            'client-chat/take/in_progress',
            'client-chat/take/idle',
            'client-chat/take/hold'
        ];

        foreach ($permissions as $permissionName) {
            if ($permission = $auth->getPermission($permissionName)) {
                $auth->remove($permission);
            }
        }

        foreach ($rules as $ruleName) {
            if ($rule = $auth->getRule($ruleName)) {
                $auth->remove($rule);
            }
        }
    }

    private function addPermissionsToRole(...$permissions)
    {
        $auth = Yii::$app->authManager;

        foreach ($this->roles as $item) {
            if (!$role = $auth->getRole($item)) {
                continue;
            }
            foreach ($permissions as $permission) {
                $auth->addChild($role, $permission);
            }
        }
    }
}
