<?php

use sales\rbac\rules\cases\take\CasesTakeFollowUpRule;
use sales\rbac\rules\cases\take\CasesTakeOverRule;
use sales\rbac\rules\cases\take\CasesTakePendingRule;
use sales\rbac\rules\cases\take\CasesTakeRule;
use sales\rbac\rules\cases\take\CasesTakeTrashOwnRule;
use sales\rbac\rules\cases\take\CasesTakeTrashRule;
use sales\rbac\rules\cases\update\CasesUpdateActiveOwnRule;
use sales\rbac\rules\cases\update\CasesUpdateActiveRule;
use sales\rbac\rules\cases\view\CasesViewFollowUpRule;
use sales\rbac\rules\cases\view\CasesViewPendingRule;
use sales\rbac\rules\cases\view\CasesViewProcessingOwnRule;
use sales\rbac\rules\cases\view\CasesViewProcessingRule;
use sales\rbac\rules\cases\view\CasesViewSolvedOwnRule;
use sales\rbac\rules\cases\view\CasesViewSolvedRule;
use sales\rbac\rules\cases\view\CasesViewTrashOwnRule;
use sales\rbac\rules\cases\view\CasesViewTrashRule;
use yii\db\Migration;

/**
 * Class m200425_173242_add_case_permissions
 */
class m200425_173242_add_case_permissions extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $auth = Yii::$app->authManager;

        $casesView = $auth->createPermission('cases/view');
        $casesView->description = 'Case View';
        $auth->add($casesView);

        $casesViewPendingRule = new CasesViewPendingRule();
        $auth->add($casesViewPendingRule);
        $casesViewPending = $auth->createPermission('cases/view_Pending');
        $casesViewPending->description = 'Case view Pending status';
        $casesViewPending->ruleName = $casesViewPendingRule->name;
        $auth->add($casesViewPending);
        $auth->addChild($casesViewPending, $casesView);

        $casesViewProcessingRule = new CasesViewProcessingRule();
        $auth->add($casesViewProcessingRule);
        $casesViewProcessing = $auth->createPermission('cases/view_Processing');
        $casesViewProcessing->description = 'Case view Processing status';
        $casesViewProcessing->ruleName = $casesViewProcessingRule->name;
        $auth->add($casesViewProcessing);
        $auth->addChild($casesViewProcessing, $casesView);

        $casesViewProcessingOwnRule = new CasesViewProcessingOwnRule();
        $auth->add($casesViewProcessingOwnRule);
        $casesViewProcessingOwn = $auth->createPermission('cases/view_Processing_Own');
        $casesViewProcessingOwn->description = 'Case view Processing status and User is Owner';
        $casesViewProcessingOwn->ruleName = $casesViewProcessingOwnRule->name;
        $auth->add($casesViewProcessingOwn);
        $auth->addChild($casesViewProcessingOwn, $casesView);

        $casesViewFollowUpRule = new CasesViewFollowUpRule();
        $auth->add($casesViewFollowUpRule);
        $casesViewFollowUp = $auth->createPermission('cases/view_FollowUp');
        $casesViewFollowUp->description = 'Case view Follow Up status';
        $casesViewFollowUp->ruleName = $casesViewFollowUpRule->name;
        $auth->add($casesViewFollowUp);
        $auth->addChild($casesViewFollowUp, $casesView);

        $casesViewTrashRule = new CasesViewTrashRule();
        $auth->add($casesViewTrashRule);
        $casesViewTrash = $auth->createPermission('cases/view_Trash');
        $casesViewTrash->description = 'Case view Trash status';
        $casesViewTrash->ruleName = $casesViewTrashRule->name;
        $auth->add($casesViewTrash);
        $auth->addChild($casesViewTrash, $casesView);

        $casesViewTrashOwnRule = new CasesViewTrashOwnRule();
        $auth->add($casesViewTrashOwnRule);
        $casesViewTrashOwn = $auth->createPermission('cases/view_Trash_Own');
        $casesViewTrashOwn->description = 'Case view Trash status and User is Owner';
        $casesViewTrashOwn->ruleName = $casesViewTrashOwnRule->name;
        $auth->add($casesViewTrashOwn);
        $auth->addChild($casesViewTrashOwn, $casesView);

        $casesViewSolvedRule = new CasesViewSolvedRule();
        $auth->add($casesViewSolvedRule);
        $casesViewSolved = $auth->createPermission('cases/view_Solved');
        $casesViewSolved->description = 'Case view Solved status';
        $casesViewSolved->ruleName = $casesViewSolvedRule->name;
        $auth->add($casesViewSolved);
        $auth->addChild($casesViewSolved, $casesView);

        $casesViewSolvedOwnRule = new CasesViewSolvedOwnRule();
        $auth->add($casesViewSolvedOwnRule);
        $casesViewSolvedOwn = $auth->createPermission('cases/view_Solved_Own');
        $casesViewSolvedOwn->description = 'Case view Solved status and User is Owner';
        $casesViewSolvedOwn->ruleName = $casesViewSolvedOwnRule->name;
        $auth->add($casesViewSolvedOwn);
        $auth->addChild($casesViewSolvedOwn, $casesView);

        $casesTakeRule = new CasesTakeRule();
        $auth->add($casesTakeRule);
        $casesTake = $auth->createPermission('cases/take');
        $casesTake->description = 'Case Take. Case Status is not Processing and User is not Owner';
        $casesTake->ruleName = $casesTakeRule->name;
        $auth->add($casesTake);

        $casesTakePendingRule = new CasesTakePendingRule();
        $auth->add($casesTakePendingRule);
        $casesTakePending = $auth->createPermission('cases/take_Pending');
        $casesTakePending->description = 'Case Take. Status is Pending and User is not Owner';
        $casesTakePending->ruleName = $casesTakePendingRule->name;
        $auth->add($casesTakePending);
        $auth->addChild($casesTakePending, $casesTake);

        $casesTakeFollowUpRule = new CasesTakeFollowUpRule();
        $auth->add($casesTakeFollowUpRule);
        $casesTakeFollowUp = $auth->createPermission('cases/take_FollowUp');
        $casesTakeFollowUp->description = 'Case Take. Status is Follow Up and User is not Owner';
        $casesTakeFollowUp->ruleName = $casesTakeFollowUpRule->name;
        $auth->add($casesTakeFollowUp);
        $auth->addChild($casesTakeFollowUp, $casesTake);

        $casesTakeTrashRule = new CasesTakeTrashRule();
        $auth->add($casesTakeTrashRule);
        $casesTakeTrash = $auth->createPermission('cases/take_Trash');
        $casesTakeTrash->description = 'Case Take. Status is Trash';
        $casesTakeTrash->ruleName = $casesTakeTrashRule->name;
        $auth->add($casesTakeTrash);
        $auth->addChild($casesTakeTrash, $casesTake);

        $casesTakeTrashOwnRule = new CasesTakeTrashOwnRule();
        $auth->add($casesTakeTrashOwnRule);
        $casesTakeTrashOwn = $auth->createPermission('cases/take_Trash_Own');
        $casesTakeTrashOwn->description = 'Case Take. Status is Trash and User is Owner';
        $casesTakeTrashOwn->ruleName = $casesTakeTrashOwnRule->name;
        $auth->add($casesTakeTrashOwn);
        $auth->addChild($casesTakeTrashOwn, $casesTake);

        $casesTakeOverRule = new CasesTakeOverRule();
        $auth->add($casesTakeOverRule);
        $casesTakeOver = $auth->createPermission('cases/takeOver');
        $casesTakeOver->description = 'Case Take Over. Case Status is Processing and User is not Owner';
        $casesTakeOver->ruleName = $casesTakeOverRule->name;
        $auth->add($casesTakeOver);

        $casesUpdate = $auth->createPermission('cases/update');
        $casesUpdate->description = 'Case Update';
        $auth->add($casesUpdate);

        $casesUpdateActiveRule = new CasesUpdateActiveRule();
        $auth->add($casesUpdateActiveRule);
        $casesUpdateActive = $auth->createPermission('cases/update_Active');
        $casesUpdateActive->description = 'Case Update. Case Status is in (Pending, Processing, Follow Up)';
        $casesUpdateActive->ruleName = $casesUpdateActiveRule->name;
        $auth->add($casesUpdateActive);
        $auth->addChild($casesUpdateActive, $casesUpdate);

        $casesUpdateActiveOwnRule = new CasesUpdateActiveOwnRule();
        $auth->add($casesUpdateActiveOwnRule);
        $casesUpdateActiveOwn = $auth->createPermission('cases/update_Active_Own');
        $casesUpdateActiveOwn->description = 'Case Update. User is Owner and Case Status is in (Pending, Processing, Follow Up)';
        $casesUpdateActiveOwn->ruleName = $casesUpdateActiveOwnRule->name;
        $auth->add($casesUpdateActiveOwn);
        $auth->addChild($casesUpdateActiveOwn, $casesUpdate);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $auth = Yii::$app->authManager;

        $rules = [
            'CasesUpdateActiveOwnRule',
            'CasesUpdateActiveRule',
            'CasesTakeOverRule',
            'CasesTakeTrashOwnRule',
            'CasesTakeTrashRule',
            'CasesTakeFollowUpRule',
            'CasesTakePendingRule',
            'CasesTakeRule',
            'CasesViewSolvedOwnRule',
            'CasesViewSolvedRule',
            'CasesViewTrashOwnRule',
            'CasesViewTrashRule',
            'CasesViewFollowUpRule',
            'CasesViewProcessingOwnRule',
            'CasesViewProcessingRule',
            'CasesViewPendingRule',
        ];
        $permissions = [
            'cases/update_Active_Own',
            'cases/update_Active',
            'cases/update',
            'cases/takeOver',
            'cases/take_Trash_Own',
            'cases/take_Trash',
            'cases/take_FollowUp',
            'cases/take_Pending',
            'cases/take',
            'cases/view_Solved_Own',
            'cases/view_Solved',
            'cases/view_Trash_Own',
            'cases/view_Trash',
            'cases/view_FollowUp',
            'cases/view_Processing_Own',
            'cases/view_Processing',
            'cases/view_Pending',
            'cases/view',
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
