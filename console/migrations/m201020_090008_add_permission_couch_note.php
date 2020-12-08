<?php

use common\models\Employee;
use console\migrations\RbacMigrationService;
use sales\rbac\rules\clientChat\couchNote\ClientChatCouchNoteHoldRule;
use sales\rbac\rules\clientChat\couchNote\ClientChatCouchNoteIdleRule;
use sales\rbac\rules\clientChat\couchNote\ClientChatCouchNoteInProgressRule;
use yii\db\Migration;

/**
 * Class m201020_090008_add_permission_couch_note
 */
class m201020_090008_add_permission_couch_note extends Migration
{
    private array $roles = [
        Employee::ROLE_ADMIN,
        Employee::ROLE_SUPER_ADMIN,
        Employee::ROLE_SUPERVISION,
    ];

    private $route = [
        '/client-chat/ajax-couch-note',
    ];

    private string $couchNotePermissionName = 'client-chat/couch-note';
    private string $couchNoteInProgressPermissionName = 'client-chat/couch-note/in_progress';
    private string $couchNoteIdlePermissionName = 'client-chat/couch-note/idle';
    private string $couchNoteHoldPermissionName = 'client-chat/couch-note/hold';

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $auth = Yii::$app->authManager;

        $couchNotePermission = $auth->createPermission($this->couchNotePermissionName);
        $couchNotePermission->description = 'Client Chat couch note';
        $auth->add($couchNotePermission);

        $couchNoteProgressRule = new ClientChatCouchNoteInProgressRule();
        $auth->add($couchNoteProgressRule);
        $couchNoteInProgressPermission = $auth->createPermission($this->couchNoteInProgressPermissionName);
        $couchNoteInProgressPermission->description = 'Client Cat couch note In Progress';
        $couchNoteInProgressPermission->ruleName = $couchNoteProgressRule->name;
        $auth->add($couchNoteInProgressPermission);
        $auth->addChild($couchNoteInProgressPermission, $couchNotePermission);

        $couchNoteIdleRule = new ClientChatCouchNoteIdleRule();
        $auth->add($couchNoteIdleRule);
        $couchNoteIdlePermission = $auth->createPermission($this->couchNoteIdlePermissionName);
        $couchNoteIdlePermission->description = 'Client Cat couch note In Idle';
        $couchNoteIdlePermission->ruleName = $couchNoteIdleRule->name;
        $auth->add($couchNoteIdlePermission);
        $auth->addChild($couchNoteIdlePermission, $couchNotePermission);

        $couchNoteHoldRule = new ClientChatCouchNoteHoldRule();
        $auth->add($couchNoteHoldRule);
        $couchNoteHoldPermission = $auth->createPermission($this->couchNoteHoldPermissionName);
        $couchNoteHoldPermission->description = 'Client Cat couch note Hold';
        $couchNoteHoldPermission->ruleName = $couchNoteHoldRule->name;
        $auth->add($couchNoteHoldPermission);
        $auth->addChild($couchNoteHoldPermission, $couchNotePermission);

        (new RbacMigrationService())->up($this->route, $this->roles);
        (new RbacMigrationService())->up(
            [
                $this->couchNoteInProgressPermissionName,
                $this->couchNoteIdlePermissionName,
                $this->couchNoteHoldPermissionName,
            ],
            $this->roles
        ); // separation code for humanity
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $auth = Yii::$app->authManager;

        (new RbacMigrationService())->down($this->route, $this->roles);
        (new RbacMigrationService())->down(
            [
                $this->couchNoteInProgressPermissionName,
                $this->couchNoteIdlePermissionName,
                $this->couchNoteHoldPermissionName,
            ],
            $this->roles
        );

        $rules = [
            'ClientChatCouchNoteInProgressRule',
            'ClientChatCouchNoteIdleRule',
            'ClientChatCouchNoteHoldRule',
        ];
        $permissions = [
            $this->couchNotePermissionName,
            $this->couchNoteInProgressPermissionName,
            $this->couchNoteIdlePermissionName,
            $this->couchNoteHoldPermissionName,
        ];

        foreach ($rules as $ruleName) {
            if ($rule = $auth->getRule($ruleName)) {
                $auth->remove($rule);
            }
        }

        foreach ($permissions as $permissionName) {
            if ($permission = $auth->getPermission($permissionName)) {
                $auth->remove($permission);
            }
        }
    }
}
