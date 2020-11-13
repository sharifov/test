<?php

use common\models\Employee;
use sales\rbac\rules\clientChat\close\ClientChatCloseInProgressRule;
use sales\rbac\rules\clientChat\close\ClientChatCloseNewRule;
use sales\rbac\rules\clientChat\close\ClientChatClosePendingRule;
use sales\rbac\rules\clientChat\close\ClientChatCloseTransferRule;
use sales\rbac\rules\clientChat\manage\ClientChatManageGroupRule;
use sales\rbac\rules\clientChat\manage\ClientChatManageOwnerRule;
use sales\rbac\rules\clientChat\transfer\ClientChatTransferInProgressRule;
use sales\rbac\rules\clientChat\transfer\ClientChatTransferNewRule;
use sales\rbac\rules\clientChat\transfer\ClientChatTransferPendingRule;
use sales\rbac\rules\clientChat\view\ClientChatViewGroupRule;
use sales\rbac\rules\clientChat\view\ClientChatViewOwnerRule;
use yii\db\Migration;

/**
 * Class m201006_053934_create_client_chat_permissions
 */
class m201006_053934_create_client_chat_permissions extends Migration
{
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

        //---
        $clientChatManagePermission = $auth->createPermission('client-chat/manage');
        $clientChatManagePermission->description = 'Client chat manage';
        $auth->add($clientChatManagePermission);

        $clientChatManageOwnerRule = new ClientChatManageOwnerRule();
        $auth->add($clientChatManageOwnerRule);
        $clientChatManageOwnerPermission = $auth->createPermission('client-chat/manage/owner');
        $clientChatManageOwnerPermission->description = 'Client chat manage owner';
        $clientChatManageOwnerPermission->ruleName = $clientChatManageOwnerRule->name;
        $auth->add($clientChatManageOwnerPermission);
        $auth->addChild($clientChatManageOwnerPermission, $clientChatManagePermission);

        $clientChatManageGroupRule = new ClientChatManageGroupRule();
        $auth->add($clientChatManageGroupRule);
        $clientChatManageGroupPermission = $auth->createPermission('client-chat/manage/group');
        $clientChatManageGroupPermission->description = 'Client chat manage group';
        $clientChatManageGroupPermission->ruleName = $clientChatManageGroupRule->name;
        $auth->add($clientChatManageGroupPermission);
        $auth->addChild($clientChatManageGroupPermission, $clientChatManagePermission);

        //---
        $clientChatViewPermission = $auth->createPermission('client-chat/view');
        $clientChatViewPermission->description = 'Client chat view';
        $auth->add($clientChatViewPermission);

        $clientChatViewOwnerRule = new ClientChatViewOwnerRule();
        $auth->add($clientChatViewOwnerRule);
        $clientChatViewOwnerPermission = $auth->createPermission('client-chat/view/owner');
        $clientChatViewOwnerPermission->description = 'Client chat view owner';
        $clientChatViewOwnerPermission->ruleName = $clientChatViewOwnerRule->name;
        $auth->add($clientChatViewOwnerPermission);
        $auth->addChild($clientChatViewOwnerPermission, $clientChatViewPermission);

        $clientChatViewGroupRule = new ClientChatViewGroupRule();
        $auth->add($clientChatViewGroupRule);
        $clientChatViewGroupPermission = $auth->createPermission('client-chat/view/group');
        $clientChatViewGroupPermission->description = 'Client chat view group';
        $clientChatViewGroupPermission->ruleName = $clientChatViewGroupRule->name;
        $auth->add($clientChatViewGroupPermission);
        $auth->addChild($clientChatViewGroupPermission, $clientChatViewPermission);

        //---
        $clientChatClosePermission = $auth->createPermission('client-chat/close');
        $auth->add($clientChatClosePermission);

        $clientChatCloseNewRule = new ClientChatCloseNewRule();
        $auth->add($clientChatCloseNewRule);
        $clientChatCloseNewPermission = $auth->createPermission('client-chat/close/new');
        $clientChatCloseNewPermission->description = 'Client chat close new';
        $clientChatCloseNewPermission->ruleName = $clientChatCloseNewRule->name;
        $auth->add($clientChatCloseNewPermission);
        $auth->addChild($clientChatCloseNewPermission, $clientChatClosePermission);

        $clientChatClosePendingRule = new ClientChatClosePendingRule();
        $auth->add($clientChatClosePendingRule);
        $clientChatClosePendingPermission = $auth->createPermission('client-chat/close/pending');
        $clientChatClosePendingPermission->description = 'Client chat close pending';
        $clientChatClosePendingPermission->ruleName = $clientChatClosePendingRule->name;
        $auth->add($clientChatClosePendingPermission);
        $auth->addChild($clientChatClosePendingPermission, $clientChatClosePermission);

        $clientChatCloseInProgressRule = new ClientChatCloseInProgressRule();
        $auth->add($clientChatCloseInProgressRule);
        $clientChatCloseInProgressPermission = $auth->createPermission('client-chat/close/in_progress');
        $clientChatCloseInProgressPermission->description = 'Client chat close in progress';
        $clientChatCloseInProgressPermission->ruleName = $clientChatCloseInProgressRule->name;
        $auth->add($clientChatCloseInProgressPermission);
        $auth->addChild($clientChatCloseInProgressPermission, $clientChatClosePermission);

        $clientChatCloseTransferRule = new ClientChatCloseTransferRule();
        $auth->add($clientChatCloseTransferRule);
        $clientChatCloseTransferPermission = $auth->createPermission('client-chat/close/transfer');
        $clientChatCloseTransferPermission->description = 'Client chat close transfer';
        $clientChatCloseTransferPermission->ruleName = $clientChatCloseTransferRule->name;
        $auth->add($clientChatCloseTransferPermission);
        $auth->addChild($clientChatCloseTransferPermission, $clientChatClosePermission);

        //---
        $clientChatTransferPermission = $auth->createPermission('client-chat/transfer');
        $auth->add($clientChatTransferPermission);

        $clientChatTransferNewRule = new ClientChatTransferNewRule();
        $auth->add($clientChatTransferNewRule);
        $clientChatTransferNewPermission = $auth->createPermission('client-chat/transfer/new');
        $clientChatTransferNewPermission->description = 'Client chat transfer new';
        $clientChatTransferNewPermission->ruleName = $clientChatTransferNewRule->name;
        $auth->add($clientChatTransferNewPermission);
        $auth->addChild($clientChatTransferNewPermission, $clientChatTransferPermission);

        $clientChatTransferPendingRule = new ClientChatTransferPendingRule();
        $auth->add($clientChatTransferPendingRule);
        $clientChatTransferPendingPermission = $auth->createPermission('client-chat/transfer/pending');
        $clientChatTransferPendingPermission->description = 'Client chat transfer pending';
        $clientChatTransferPendingPermission->ruleName = $clientChatTransferPendingRule->name;
        $auth->add($clientChatTransferPendingPermission);
        $auth->addChild($clientChatTransferPendingPermission, $clientChatTransferPermission);

        $clientChatTransferInProgressRule = new ClientChatTransferInProgressRule();
        $auth->add($clientChatTransferInProgressRule);
        $clientChatTransferInProgressPermission = $auth->createPermission('client-chat/transfer/in_progress');
        $clientChatTransferInProgressPermission->description = 'Client chat transfer in progress';
        $clientChatTransferInProgressPermission->ruleName = $clientChatTransferInProgressRule->name;
        $auth->add($clientChatTransferInProgressPermission);
        $auth->addChild($clientChatTransferInProgressPermission, $clientChatTransferPermission);

        $clientChatNotesViewPermission = $auth->createPermission('client-chat/notes/view');
        $clientChatNotesViewPermission->description = 'Client chat notes view';
        $auth->add($clientChatNotesViewPermission);

        $clientChatNotesAddPermission = $auth->createPermission('client-chat/notes/add');
        $clientChatNotesAddPermission->description = 'Client chat notes add';
        $auth->add($clientChatNotesAddPermission);
        $auth->addChild($clientChatNotesAddPermission, $clientChatNotesViewPermission);

        $clientChatNotesDeletePermission = $auth->createPermission('client-chat/notes/delete');
        $clientChatNotesDeletePermission->description = 'Client chat notes delete';
        $auth->add($clientChatNotesDeletePermission);
        $auth->addChild($clientChatNotesDeletePermission, $clientChatNotesViewPermission);

        $this->addPermissionsToRole(
            $clientChatManagePermission,
            $clientChatViewPermission,
            $clientChatClosePermission,
            $clientChatTransferPermission,
            $clientChatNotesAddPermission,
            $clientChatNotesDeletePermission
        );
    }
    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $auth = Yii::$app->authManager;

        $rules = [
            'ClientChatManageOwnerRule',
            'ClientChatManageGroupRule',
            'ClientChatViewOwnerRule',
            'ClientChatViewGroupRule',
            'ClientChatCloseNewRule',
            'ClientChatClosePendingRule',
            'ClientChatCloseInProgressRule',
            'ClientChatCloseTransferRule',
            'ClientChatTransferNewRule',
            'ClientChatTransferPendingRule',
            'ClientChatTransferInProgressRule',
        ];

        $permissions = [
            'client-chat/manage',
            'client-chat/manage/owner',
            'client-chat/manage/group',
            'client-chat/view',
            'client-chat/view/owner',
            'client-chat/view/group',
            'client-chat/close',
            'client-chat/close/new',
            'client-chat/close/pending',
            'client-chat/close/in_progress',
            'client-chat/close/transfer',
            'client-chat/transfer',
            'client-chat/transfer/new',
            'client-chat/transfer/pending',
            'client-chat/transfer/in_progress',
            'client-chat/notes/view',
            'client-chat/notes/add',
            'client-chat/notes/delete',
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
