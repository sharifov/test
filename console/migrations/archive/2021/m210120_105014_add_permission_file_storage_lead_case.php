<?php

use common\models\Employee;
use sales\rbac\rules\cases\view\files\CaseViewFilesViewEmptyOwnerRule;
use sales\rbac\rules\cases\view\files\CaseViewFilesViewGroupRule;
use sales\rbac\rules\cases\view\files\CaseViewFilesViewIsOwnerRule;
use sales\rbac\rules\lead\view\files\LeadViewFilesViewEmptyOwnerRule;
use sales\rbac\rules\lead\view\files\LeadViewFilesViewGroupRule;
use sales\rbac\rules\lead\view\files\LeadViewFilesViewIsOwnerRule;
use yii\db\Migration;

/**
 * Class m210120_105014_add_permission_file_storage_lead_case
 */
class m210120_105014_add_permission_file_storage_lead_case extends Migration
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

        //lead
        $leadView = $auth->createPermission('lead-view/files/view');
        $leadView->description = 'Lead vew files view';
        $auth->add($leadView);

        $leadViewAll = $auth->createPermission('lead-view/files/view/all');
        $leadViewAll->description = 'Lead vew files view full access';
        $auth->add($leadViewAll);
        $auth->addChild($leadViewAll, $leadView);

        $leadViewOwnerRule = new LeadViewFilesViewIsOwnerRule();
        $auth->add($leadViewOwnerRule);
        $leadViewOwner = $auth->createPermission('lead-view/files/view/owner');
        $leadViewOwner->description = 'Lead view files is owner';
        $leadViewOwner->ruleName = $leadViewOwnerRule->name;
        $auth->add($leadViewOwner);
        $auth->addChild($leadViewOwner, $leadView);

        $leadViewGroupRule = new LeadViewFilesViewGroupRule();
        $auth->add($leadViewGroupRule);
        $leadViewGroup = $auth->createPermission('lead-view/files/view/group');
        $leadViewGroup->description = 'Lead view files is owner';
        $leadViewGroup->ruleName = $leadViewGroupRule->name;
        $auth->add($leadViewGroup);
        $auth->addChild($leadViewGroup, $leadView);

        $leadViewEmptyOwnerRule = new LeadViewFilesViewEmptyOwnerRule();
        $auth->add($leadViewEmptyOwnerRule);
        $leadViewEmptyOwner = $auth->createPermission('lead-view/files/view/empty');
        $leadViewEmptyOwner->description = 'Lead view files is owner';
        $leadViewEmptyOwner->ruleName = $leadViewEmptyOwnerRule->name;
        $auth->add($leadViewEmptyOwner);
        $auth->addChild($leadViewEmptyOwner, $leadView);

        $leadViewUpload = $auth->createPermission('lead-view/files/upload');
        $leadViewUpload->description = 'Lead vew files upload';
        $auth->add($leadViewUpload);

        //case
        $caseView = $auth->createPermission('case-view/files/view');
        $caseView->description = 'Case vew files view';
        $auth->add($caseView);

        $caseViewAll = $auth->createPermission('case-view/files/view/all');
        $caseViewAll->description = 'Case vew files view full access';
        $auth->add($caseViewAll);
        $auth->addChild($caseViewAll, $caseView);

        $caseViewOwnerRule = new CaseViewFilesViewIsOwnerRule();
        $auth->add($caseViewOwnerRule);
        $caseViewOwner = $auth->createPermission('case-view/files/view/owner');
        $caseViewOwner->description = 'Case view files is owner';
        $caseViewOwner->ruleName = $caseViewOwnerRule->name;
        $auth->add($caseViewOwner);
        $auth->addChild($caseViewOwner, $caseView);

        $caseViewGroupRule = new CaseViewFilesViewGroupRule();
        $auth->add($caseViewGroupRule);
        $caseViewGroup = $auth->createPermission('case-view/files/view/group');
        $caseViewGroup->description = 'Case view files is owner';
        $caseViewGroup->ruleName = $caseViewGroupRule->name;
        $auth->add($caseViewGroup);
        $auth->addChild($caseViewGroup, $caseView);

        $caseViewEmptyOwnerRule = new CaseViewFilesViewEmptyOwnerRule();
        $auth->add($caseViewEmptyOwnerRule);
        $caseViewEmptyOwner = $auth->createPermission('case-view/files/view/empty');
        $caseViewEmptyOwner->description = 'Case view files is owner';
        $caseViewEmptyOwner->ruleName = $caseViewEmptyOwnerRule->name;
        $auth->add($caseViewEmptyOwner);
        $auth->addChild($caseViewEmptyOwner, $caseView);

        $caseViewUpload = $auth->createPermission('case-view/files/upload');
        $caseViewUpload->description = 'Case vew files upload';
        $auth->add($caseViewUpload);

        foreach ($this->roles as $item) {
            if ($role = $auth->getRole($item)) {
                $auth->addChild($role, $leadViewAll);
                $auth->addChild($role, $leadViewUpload);
                $auth->addChild($role, $caseViewAll);
                $auth->addChild($role, $caseViewUpload);
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $auth = Yii::$app->authManager;

        $rules = [
            'LeadViewFilesViewIsOwnerRule',
            'LeadViewFilesViewGroupRule',
            'LeadViewFilesViewEmptyOwnerRule',
            'CaseViewFilesViewIsOwnerRule',
            'CaseViewFilesViewGroupRule',
            'CaseViewFilesViewEmptyOwnerRule',
        ];

        $permissions = [
            'lead-view/files/view',
            'lead-view/files/view/all',
            'lead-view/files/view/owner',
            'lead-view/files/view/group',
            'lead-view/files/view/empty',
            'lead-view/files/upload',
            'case-view/files/view',
            'case-view/files/view/all',
            'case-view/files/view/owner',
            'case-view/files/view/group',
            'case-view/files/view/empty',
            'case-view/files/upload',
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
}
