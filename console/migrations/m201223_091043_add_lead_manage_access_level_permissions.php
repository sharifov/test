<?php

use yii\db\Migration;
use sales\rbac\rules\lead\manage\LeadManageIsOwnerRule;
use sales\rbac\rules\lead\manage\LeadManageEmptyOwnerRule;
use sales\rbac\rules\lead\manage\LeadManageGroupRule;
use common\models\Employee;
use console\migrations\RbacMigrationService;

/**
 * Class m201223_091043_add_lead_manage_access_level_permissions
 */
class m201223_091043_add_lead_manage_access_level_permissions extends Migration
{
    private string $leadManage = 'lead/manage';
    private string $leadManageAll = 'lead/manage/all';
    private string $leadManageOwner = 'lead/manage/owner';
    private string $leadManageEmpty = 'lead/manage/empty';
    private string $leadManageGroup = 'lead/manage/group';

    private array $highPriorityRoles = [
        Employee::ROLE_ADMIN,
        Employee::ROLE_SUPER_ADMIN
    ];

    private array $mediumPriorityRoles = [
        Employee::ROLE_SUPERVISION,
        Employee::ROLE_SALES_SENIOR,
        Employee::ROLE_EXCHANGE_SENIOR,
        Employee::ROLE_EX_SUPER
    ];

    private array $checkedRoles = [];

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $auth = Yii::$app->authManager;

        $leadManagePermission = $auth->createPermission($this->leadManage);
        $leadManagePermission->description = 'Lead Manage';
        $auth->add($leadManagePermission);

        $leadManageAllPermission = $auth->createPermission($this->leadManageAll);
        $leadManageAllPermission->description = 'Lead Manage all';
        $auth->add($leadManageAllPermission);
        $auth->addChild($leadManageAllPermission, $leadManagePermission);

        $leadManageOwnerRule = new LeadManageIsOwnerRule();
        $auth->add($leadManageOwnerRule);
        $leadManageOwnerPermission = $auth->createPermission($this->leadManageOwner);
        $leadManageOwnerPermission->description = 'Lead Manage if user is Owner';
        $leadManageOwnerPermission->ruleName = $leadManageOwnerRule->name;
        $auth->add($leadManageOwnerPermission);
        $auth->addChild($leadManageOwnerPermission, $leadManagePermission);

        $leadManageEmptyOwnerRule = new LeadManageEmptyOwnerRule();
        $auth->add($leadManageEmptyOwnerRule);
        $leadManageEmptyPermission = $auth->createPermission($this->leadManageEmpty);
        $leadManageEmptyPermission->description = 'Lead Manage if Owner is empty';
        $leadManageEmptyPermission->ruleName = $leadManageEmptyOwnerRule->name;
        $auth->add($leadManageEmptyPermission);
        $auth->addChild($leadManageEmptyPermission, $leadManagePermission);

        $leadManageGroupRule = new LeadManageGroupRule();
        $auth->add($leadManageGroupRule);
        $leadManageGroupPermission = $auth->createPermission($this->leadManageGroup);
        $leadManageGroupPermission->description = 'Lead Manage if user is in common group with owner';
        $leadManageGroupPermission->ruleName = $leadManageGroupRule->name;
        $auth->add($leadManageGroupPermission);
        $auth->addChild($leadManageGroupPermission, $leadManagePermission);

        foreach ($auth->getRoles() as $role) {
            foreach ($auth->getPermissionsByRole($role->name) as $authPermission) {
                if ($authPermission->name === '/lead/processing') {
                    $this->checkedRoles[] = $role->name;
                }
            }
        }

        (new RbacMigrationService())->up([$this->leadManageOwner], $this->checkedRoles);

        (new RbacMigrationService())->up(
            [
                $this->leadManage,
                $this->leadManageAll,
                $this->leadManageOwner,
                $this->leadManageEmpty,
                $this->leadManageGroup,
            ],
            $this->highPriorityRoles
        );

        (new RbacMigrationService())->up(
            [
                $this->leadManageOwner,
                $this->leadManageEmpty,
            ],
            $this->mediumPriorityRoles
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $auth = Yii::$app->authManager;

        $rules = [
            'LeadManageIsOwnerRule',
            'LeadManageEmptyOwnerRule',
            'LeadManageGroupRule'
        ];

        $permissions = [
            $this->leadManage,
            $this->leadManageAll,
            $this->leadManageOwner,
            $this->leadManageEmpty,
            $this->leadManageGroup
        ];

        foreach ($auth->getRoles() as $role) {
            foreach ($auth->getPermissionsByRole($role->name) as $authPermission) {
                if ($authPermission->name === '/lead/processing') {
                    $this->checkedRoles[] = $role->name;
                }
            }
        }

        (new RbacMigrationService())->down([$this->leadManageOwner], $this->checkedRoles);

        (new RbacMigrationService())->down(
            [
                $this->leadManage,
                $this->leadManageAll,
                $this->leadManageOwner,
                $this->leadManageEmpty,
                $this->leadManageGroup,
            ],
            $this->highPriorityRoles
        );

        (new RbacMigrationService())->up(
            [
                $this->leadManageOwner,
                $this->leadManageEmpty,
            ],
            $this->mediumPriorityRoles
        );

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
}
